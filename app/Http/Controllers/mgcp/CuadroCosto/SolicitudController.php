<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\User;
use App\Models\mgcp\CuadroCosto\CcSolicitud;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\mgcp\CuadroCosto\CuadroCostoHelper;
use App\Helpers\mgcp\CuadroCosto\RequerimientoHelper;
use App\Mail\mgcp\CuadroCosto\AprobacionCuadro;
use App\Mail\mgcp\CuadroCosto\RespuestaSolicitud;
use App\Mail\mgcp\CuadroCosto\RetiroAprobacionCuadro;
use App\Mail\mgcp\CuadroCosto\SolicitudAprobacion;
use App\Models\Almacen\Requerimiento;
use App\Models\mgcp\CuadroCosto\AprobadorTres;
use App\Models\mgcp\CuadroCosto\AprobadorUno;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\mgcp\Usuario\Notificacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class SolicitudController extends Controller
{
    private $nombreFormulario = 'Solicitudes de aprobación de cuadros de presupuesto';

    public function listar(Request $request)
    {
        $solicitudes = CcSolicitud::with('enviadaPor', 'enviadaA', 'tiposolicitud')->where('id_cc', $request->idCuadro)->get();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'CDP: ' . CuadroCosto::find($request->idCuadro)->oportunidad->codigo_oportunidad);
        return response()->json($solicitudes, 200);
    }

    public function eliminar(Request $request)
    {
        $solicitud = CcSolicitud::find($request->idSolicitud);
        if ($solicitud->respuesta != null) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'La solicitud no se puede eliminar porque tiene una respuesta.'), 200);
        }

        if ($solicitud->enviada_a != Auth::user()->id) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'La solicitud no se puede eliminar porque no fue enviada al usuario que intenta eliminarla.'), 200);
        }

        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $solicitud->getTable(), $solicitud, null, 'CDP: ' . CuadroCosto::find($solicitud->id_cc)->oportunidad->codigo_oportunidad);
        $solicitud->delete();

        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la solicitud'), 200);
    }

    public function nueva(Request $request)
    {
        $cuadro = CuadroCosto::find($request->cuadro);
        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);

        if ($oportunidad == null) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'La oportunidad no existe'), 200);
        }
        if (($request->tipo == 4 || $request->tipo == 2)  && empty($request->comentario)) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'Para este tipo de solicitud, es necesario ingresar un motivo/comentario'), 200);
        }
        if ($cuadro->estado_aprobacion == 2) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'No se puede crear una nueva solicitud debido a que existe una solicitud pendiente de aprobación.'), 200);
        }
        if (($cuadro->estado_aprobacion == 3 || $cuadro->estado_aprobacion == 4) && ($request->tipo == 1 || $request->tipo == 4)) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro está aprobado. La solicitud no procede'), 200);
        }
        $detallesCuadro = CuadroCostoHelper::obtenerDetallesFilas($request->cuadro);

        $idUsuarioMgcp = 44;
        $codigosReapertura = [2, 3];
        $aprobacionAutomatica = false;
        $margenCuadro = str_replace([',', '%'], '', $cuadro->margen_ganancia);
        
        DB::beginTransaction();
        try {

            $solicitud = new CcSolicitud();
            $solicitud->id_cc = $request->cuadro;
            $solicitud->fecha_solicitud = new Carbon();
            $solicitud->enviada_por = Auth::user()->id;
            $solicitud->id_tipo = $request->tipo;
            $solicitud->comentario_solicitante = $request->comentario;
            $solicitud->estado_cuadro = $cuadro->estado_aprobacion;
            $solicitud->margen_cuadro = $margenCuadro;

            if (in_array($request->tipo, $codigosReapertura)) { //2: Retirar aprobación, 3: Reapertura
                $aprobacionAutomatica = true;
            } else {
                $solicitudReapertura = CcSolicitud::where('id_cc', $request->cuadro)->whereIn('id_tipo', $codigosReapertura)->orderBy('id', 'DESC')->first();
                if ($solicitudReapertura != null) {
                    $aprobacionAutomatica = $margenCuadro >= $solicitudReapertura->margen_cuadro;
                } else {
                    $aprobacionAutomatica = false;
                }
            }

            if ($aprobacionAutomatica) {
                $mensaje = 'Su solicitud ha sido aprobada de forma automática. La página se actualizará';
                $solicitud->enviada_a = $idUsuarioMgcp;
                $solicitud->fecha_respuesta = new Carbon();
                $solicitud->aprobada = true;
                $solicitud->comentario_aprobador = 'Aprobado de forma automática por el sistema. Motivo: ';
                if (in_array($request->tipo, $codigosReapertura)) {
                    $solicitud->comentario_aprobador .= 'Retirar aprobación/reapertura de cuadro.';
                    $cuadro->estado_aprobacion = 1;
                } else {
                    $solicitud->comentario_aprobador .= 'Margen de ganancia igual o superior a la aprobación anterior.';
                    $cuadro->estado_aprobacion = $request->tipo == 1 ? 3 : 5;
                }
            } else {
                $aprobador = $this->obtenerAprobadorPorMonto($detallesCuadro);
                $solicitud->enviada_a = $aprobador;
                $solicitud->fecha_respuesta = null;
                $solicitud->aprobada = null;
                $destinatario = User::find($aprobador);
                $cuadro->estado_aprobacion = 2;
                $this->enviarSolicitudAprobacion($request->tipo, $aprobador, $oportunidad, $detallesCuadro, $request->comentario);
                $mensaje = "Su solicitud ha sido enviada a $destinatario->name. La página se actualizará"; //.'margen actual: '.$margenCuadro.', anterior: '. $solicitudReapertura->margen_cuadro
            }
            $solicitud->save();
            $cuadro->save();

            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $solicitud->getTable(), null, $solicitud, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad);

            if ($solicitud->aprobada == 1 && in_array($cuadro->estado_aprobacion, [3, 5])) {
                $this->enviarCorreoPorAprobacion($cuadro, $solicitud);
            }

            if ($solicitud->aprobada == 1 && in_array($cuadro->estado_aprobacion, [1, 2])) {
                $this->enviarCorreoPorRetiroAprobacion($cuadro, $solicitud);
            }

            DB::commit();
            return response()->json(array('tipo' => 'success', 'titulo' => "Solicitud enviada", "texto" => $mensaje), 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'titulo' => "Problema al procesar su solicitud", "texto" => "Por favor actualice la página e intente de nuevo. Código de error: " . $ex->getMessage()), 200);
        }
    }

    public function responder(Request $request)
    {
        $cuadro = CuadroCosto::find($request->idCuadro);
        $solicitud = CcSolicitud::where('enviada_a', Auth::user()->id)->where('id_cc', $request->idCuadro)->whereNull('aprobada')->first();
        if ($solicitud == null) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'No hay una solicitud por responder'), 200);
        }
        
        if ($cuadro->estado_aprobacion != 2) {
            if ($cuadro->estado_aprobacion == 1 && $cuadro->aprobacion_previa == 1) {
                // return response()->json(array('tipo' => 'info', 'titulo' => 'Se aprobará el cuadro de costo con aprobación previa'), 200);
            } else {
                return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro de costo no tiene una aprobación pendiente'), 200);
            }
        }
        if (!in_array($request->aprobar, array(0, 1))) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'La opción de aprobación seleccionada no es válida'), 200);
        }
        if ($solicitud->id_tipo == 4 && empty($request->comentario)) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'Para este tipo de solicitud, se requiere ingresar un comentario'), 200);
        }

        DB::beginTransaction();
        try {
            $solicitud->fecha_respuesta = new Carbon();
                $solicitud->aprobada = $request->aprobar;
                $solicitud->comentario_aprobador = $request->comentario;
            $solicitud->save();

            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $solicitud->getTable(), $solicitud->getOriginal(), $solicitud, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad);
            $cambiarEstadoCompradoOc = false;

            if ($request->aprobar == 1) {
                switch ($solicitud->id_tipo) {
                    case 1: //Aprobación de cuadro
                        $cuadro->estado_aprobacion = 3; //Cuadro aprobado, etapa de compras
                    break;
                    case 2: //Retirar aprobación de cuadro
                        $cuadro->estado_aprobacion = 1; //Cuadro en etapa inicial
                        $cambiarEstadoCompradoOc = true;
                    break;
                    case 3: //Reapertura de cuadro
                        $cuadro->estado_aprobacion = 1; //Cuadro en etapa inicial
                        $cambiarEstadoCompradoOc = true;
                    break;
                    case 4: //Aprobación de cuadro pendiente de regularización
                        $cuadro->estado_aprobacion = 5; //Aprobado - pendiente de regularización
                    break;
                    case 5: //Aprobación previa de cuadro
                        $cuadro->estado_aprobacion = 1; //Aprobado - pendiente de regularización
                        $cuadro->aprobacion_previa = 2;
                    break;
                }
            } else {
                $cuadro->estado_aprobacion = $solicitud->estado_cuadro; //Estado del cuadro antes de la solicitud
            }
            $cuadro->save();

            if ($cambiarEstadoCompradoOc) {
                OrdenCompraPropiaView::actualizarEstadoCompra($cuadro->oportunidad->ordenCompraPropia, 1);
            }

            $this->enviarRespuestaSolicitud($cuadro, $solicitud);

            if ($solicitud->aprobada == 1 && in_array($cuadro->estado_aprobacion, [3, 5])) {
                $this->enviarCorreoPorAprobacion($cuadro, $solicitud);
            }
            
            if ($solicitud->aprobada == 1 && in_array($cuadro->estado_aprobacion, [1, 2])) {
                if ($cuadro->estado_aprobacion == 1 && $cuadro->aprobacion_previa == 2) {
                    $this->enviarCorreoPorAprobacion($cuadro, $solicitud, 1);
                } else {
                    $this->enviarCorreoPorRetiroAprobacion($cuadro, $solicitud);
                }
            }
            DB::commit();
            return response()->json(array('tipo' => 'success', 'titulo' => "Respuesta registrada", 'texto' => 'La página se actualizará'), 200);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(array('tipo' => 'error', "Problema al procesar su solicitud", "texto" => "Por favor actualice la página e intente de nuevo. Código de error: " . $ex->getMessage()), 200);
        }
    }

    public function consultaSolicitudPrevia(Request $request)
    {
        $solicitud = CcSolicitud::where('id_cc', $request->idCuadro)->whereNull('aprobada')->first();
        $respuesta = 0;
        if ($solicitud == null) {
            $respuesta = 1;
        }
        return response()->json($respuesta, 200);
    }

    public function solicitudPrevia(Request $request)
    {
        DB::beginTransaction();
        try {
            $cuadro = CuadroCosto::find($request->idCuadro);
                $cuadro->aprobacion_previa = $request->valor;
            $cuadro->save();
    
            if ($request->valor == 1) {
                $solicitud = new CcSolicitud();
                    $solicitud->id_cc = $request->idCuadro;
                    $solicitud->fecha_solicitud = new Carbon();
                    $solicitud->enviada_por = Auth::user()->id;
                    $solicitud->id_tipo = 5;
                    $solicitud->comentario_solicitante = "Gestionar la solicitud para realizar las aprobaciones correspondientes";
                    $solicitud->enviada_a = 46;
                    $solicitud->estado_cuadro = 6;
                $solicitud->save();

                $titulo = "Solicitud enviada";
                $mensaje = "Su solicitud a sido enviada";
            } else {
                CcSolicitud::where('id_cc', $request->idCuadro)->where('id_tipo', 5)->delete();
                $titulo = "Se ha cancelado la solicitud";
                $mensaje = "Su solicitud a sido cancelado";
            }

            DB::commit();
            return response()->json(array('tipo' => 'success', 'titulo' => $titulo, "texto" => $mensaje), 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'titulo' => "Problema al procesar su solicitud", "texto" => "Por favor actualice la página e intente de nuevo. Código de error: " . $ex->getMessage()), 200);
        }
    }

    private function enviarCorreoPorRetiroAprobacion($cuadro, $solicitud)
    {
        $url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
        $requerimientoHelper = new RequerimientoHelper();
        $requerimiento = $requerimientoHelper->retirarAprobacionCuadroCosto($cuadro);

        // if (config('app.debug')) {
        //     Mail::to(config('global.adminEmail'))->send(new RetiroAprobacionCuadro($solicitud, $url, $cuadro->oportunidad, Auth::user(), $requerimiento));
        // } else {
            $compradores = User::obtenerPorRol(46);
            foreach ($compradores as $comprador) {
                // Mail::to($comprador->email)->send(new RetiroAprobacionCuadro($solicitud, $url, $cuadro->oportunidad, Auth::user(), $requerimiento));
                $notificacion = new Notificacion();
                    $notificacion->id_usuario = $comprador->id;
                    $notificacion->mensaje = 'Se ha retiro la aprobación del cuadro de presupuesto <strong>'.$cuadro->oportunidad->codigo_oportunidad . '</strong> solicitado por '.$solicitud->enviadaPor->name .'
                        '.(empty($comentario) ? '' : '. Comentario del usuario: ' . $comentario)
                        . '<br> Oportunidad: ' . $cuadro->oportunidad->oportunidad
                        . '<br> Responsable: ' . $cuadro->oportunidad->responsable->name
                        . '<br> Fecha límite: ' . $cuadro->oportunidad->fecha_limite
                        . '<br> Cliente: ' . $cuadro->oportunidad->entidad->nombre;
                    $notificacion->fecha = new Carbon;
                    $notificacion->url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
                    $notificacion->leido = 0;
                $notificacion->save();
            }
        // }
    }

    private function enviarCorreoPorAprobacion($cuadro, $solicitud, $replicar = 0)
    {
        $requerimientoHelper = new RequerimientoHelper();
        $url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
        if ($replicar == 0) {
            // $respuestaReplicacion = $requerimientoHelper->replicarPorCuadroCosto($cuadro->id_oportunidad);
            $requerimientoHelper->replicarPorCuadroCosto($cuadro->id_oportunidad);
        }

        // if (config('app.debug')) {
        //     Mail::to(config('global.adminEmail'))->send(new AprobacionCuadro($solicitud, $url, $cuadro->oportunidad, Auth::user(), $respuestaReplicacion));
        // } else {
            $compradores = User::obtenerPorRol(46);
            foreach ($compradores as $comprador) {
                // Mail::to('programador03@okcomputer.com.pe')->send(new AprobacionCuadro($solicitud, $url, $cuadro->oportunidad, Auth::user(), $respuestaReplicacion));
                $notificacion = new Notificacion();
                    $notificacion->id_usuario = $comprador->id;
                    $notificacion->mensaje = 'Aprobación del cuadro de presupuesto <strong>'.$cuadro->oportunidad->codigo_oportunidad . '</strong> solicitado por '.$solicitud->enviadaPor->name .'
                        '.(empty($comentario) ? '' : '. Comentario del usuario: ' . $comentario)
                        . '<br> Oportunidad: ' . $cuadro->oportunidad->oportunidad
                        . '<br> Responsable: ' . $cuadro->oportunidad->responsable->name
                        . '<br> Fecha límite: ' . $cuadro->oportunidad->fecha_limite
                        . '<br> Cliente: ' . $cuadro->oportunidad->entidad->nombre;
                    $notificacion->fecha = new Carbon;
                    $notificacion->url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
                    $notificacion->leido = 0;
                $notificacion->save();
            }
        // }
    }

    public function obtenerAprobadorPorMonto($detalles)
    {
        if (config('app.debug')) {
            return User::where('email', config('global.adminEmail'))->first()->id;
        }
        $pvt = $detalles->moneda == '$' ? $detalles->precio_venta_total * $detalles->tipo_cambio : $detalles->precio_venta_total; //La tasa de aprobación está en soles
        //$aprobadores = array();
        $aprobadorUno = AprobadorUno::where('valor_venta', '>=', $pvt)->where('margen_minimo', '<=', ceil($detalles->margen_ganancia))->orderBy('valor_venta', 'asc')->first();
        if ($aprobadorUno != null) {
            return $aprobadorUno->id_usuario;
            //$aprobadores[] = $aprobadorUno->id_usuario;
            //return $aprobadores;
        } else {
            $aprobadorTres = AprobadorTres::first();
            return $aprobadorTres->id_usuario;
        }

        //$aprobadores[] = $aprobadorTres->id_usuario;
        //return $aprobadores;
    }

    public function enviarSolicitudAprobacion($codTipoSolicitud, $usuario, $oportunidad, $detalles, $comentario)
    {
        $tipoSolicitud = '';
        $asuntoCorreo = '';
        $destinatario = User::find($usuario);
        switch ($codTipoSolicitud) {
            case 1:
                $tipoSolicitud = 'la aprobación';
                $asuntoCorreo = 'aprobación';
                break;
            case 2:
                $tipoSolicitud = 'el retiro de aprobación';
                $asuntoCorreo = 'retiro de aprobación';
                break;
            case 3:
                $tipoSolicitud = 'la reapertura';
                $asuntoCorreo = 'reapertura';
                break;
            case 4:
                $tipoSolicitud = 'la aprobación pendiente de regularización';
                $asuntoCorreo = 'aprobación pendiente de regularización';
                break;
        }

        $notificacion = new Notificacion();
            $notificacion->id_usuario = $usuario;
            $notificacion->mensaje = '<strong>' . Auth::user()->name . '</strong> ha solicitado ' . $tipoSolicitud . ' del cuadro de costo ' .
                $oportunidad->codigo_oportunidad . (empty($comentario) ? '' : '. Comentario del usuario: ' . $comentario) .
                '. Oportunidad: ' . $oportunidad->oportunidad
                . '. Cliente: ' . $oportunidad->entidad->nombre
                . '. Costo total: ' . $detalles->costo_total_format
                . '. Precio de venta total: ' . $detalles->precio_venta_total_format
                . '. Ganancia: <strong> ' . $detalles->ganancia_real_format . '</strong>'
                . '. Margen de ganancia: ' . $detalles->margen_ganancia_format;
            $notificacion->fecha = new Carbon;
            $notificacion->url = route('mgcp.cuadro-costos.detalles', ['id' => $detalles->id_oportunidad]);
            $notificacion->leido = 0;
        $notificacion->save();
        // Mail::to((config('app.debug') ? config('global.adminEmail') : $destinatario->email))->send(new SolicitudAprobacion($tipoSolicitud, $detalles, $comentario, $notificacion->url, $oportunidad, Auth::user(), $asuntoCorreo));
    }

    public function enviarRespuestaSolicitud($cuadro, $solicitud)
    {
        $url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
        $autorSolicitud = User::find($solicitud->enviada_por);
        $tipoSolicitud = '';
        switch ($solicitud->id_tipo) {
            case 1:
                $tipoSolicitud = 'aprobación';
            break;
            case 2:
                $tipoSolicitud = 'retiro de aprobación';
            break;
        }
        //Mail::to((config('app.debug') ? config('global.adminEmail') : $autorSolicitud->email))->send(new RespuestaSolicitud($solicitud, $url, $oportunidad, Auth::user(), $tipoSolicitud));
    }
}
