<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Notificacion\FechaDescargaNotificacion;
use App\Models\mgcp\AcuerdoMarco\Notificacion\Notificacion;
use App\Helpers\mgcp\EntidadHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Validator;

class NotificacionController extends Controller
{
    private $nombreFormulario = 'Notificaciones de acuerdo marco';

    public function lista()
    {
        if (!Auth::user()->tieneRol(128)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.notificacion.lista');
    }

    public function dataLista(Request $request)
    {
        if (!Auth::user()->tieneRol(128)) {
            return response()->json(array('mensaje' => 'Usuario sin permiso', 'tipo' => 'danger'), 200);
        }

        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }

        $data = Notificacion::join('mgcp_acuerdo_marco.empresas', 'empresas.id', 'id_empresa')
            ->join('mgcp_acuerdo_marco.entidades AS emitido', 'emitido.id', 'emitido_por')
            ->join('mgcp_acuerdo_marco.entidades AS destino', 'destino.id', 'destinatario')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')
            ->select([
                'notificaciones.id', 'empresas.empresa', 'emitido.nombre AS emitido_por', 'destino.nombre AS destinatario', 'descripcion_corta', 'descripcion_larga', 
                'acuerdo_marco.descripcion AS acuerdo_marco', 'orden_compra', 'asunto', 'fecha', 'estado', 'plazo'
        ])->where('notificaciones.id_empresa', Auth::user()->id_empresa);
        return datatables($data)->toJson();
    }

    public function obtenerFechasDescarga(Request $request)
    {
        $fecha = FechaDescargaNotificacion::first();
        $ultimaDescarga = new Carbon($fecha->fecha_descarga);
        echo '<p class="text-center">Última actualización: ' . $ultimaDescarga->format('d-m-Y g:i A') . ' <small style="color: #737373">(' . $ultimaDescarga->diffForHumans() . ')</small></p>';
    }

    public function obtenerHistorialNotificacion(Request $request)
    {
        if (!Auth::user()->tieneRol(128)) {
            return response()->json(array('mensaje' => 'Usuario sin permiso', 'tipo' => 'error'), 200);
        }

        $empresa = Notificacion::find($request->id)->empresa;
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 9, null, null, null, 'Historial de notificación con ID: ' . $request->id . ', empresa: ' . $empresa->empresa);
        $continuar = false;

        do {
            $portal = new PeruComprasHelper();
            $portal->login($empresa, 2);
            $data = explode("¯", $portal->enviarData(strval($request->id), "https://www.catalogos.perucompras.gob.pe/Notificacion/consultaHitorialEstado"));
            if (count($data) == 1) {
                $continuar = true;
            } else {
                $filas = explode("¬", $data[1]);
                $historiales = [];
                foreach ($filas as $fila) {
                    $data = explode("^", $fila);
                    $historial = new stdClass();
                    $historial->estado = $data[0];
                    $historial->fecha = $data[1];
                    $historial->usuario = $data[2];
                    array_push($historiales, $historial);
                }
                $continuar = false;
            }
        } while ($continuar);
        return response()->json(array('data' => $historiales, 'tipo' => 'success'), 200);
    }

    public function obtenerDetallesNotificacion(Request $request)
    {
        if (!Auth::user()->tieneRol(128)) {
            return response()->json(array('mensaje' => 'Usuario sin permiso', 'tipo' => 'error'), 200);
        }
        
        $actualizar = false;
        $continuar = false;
        $notificacionAm = Notificacion::find($request->id);
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 9, null, null, null, 'Ver notificación con ID: ' . $request->id . ', empresa: ' . $notificacionAm->empresa->empresa);

        do {
            $portal = new PeruComprasHelper();
            $portal->login($notificacionAm->empresa, 2);

            //Actualizar estado de lectura de notificación
            if ($notificacionAm->estado == 'ENVIADO') {
                $resultado = $portal->enviarData(strval($request->id . '^2'), "https://www.catalogos.perucompras.gob.pe/Notificacion/enviarCambioEstadoNotificacion");
                if ($resultado == '1') {
                    $notificacionAm->estado = 'LEIDO';
                    $notificacionAm->save();
                    $actualizar = true;
                }
            }

            //Obtener datos de notificación desde el portal de Perú Compras
            $data = explode("^", $portal->enviarData(strval($request->id), "https://www.catalogos.perucompras.gob.pe/Notificacion/consultaDetalleNotificacion"));
            if (count($data) == 1) {
                $continuar = true;
            } else {
                $notificacion = new stdClass();
                $notificacion->titulo = ucfirst(mb_strtolower($data[1]));
                $notificacion->orden_compra = $data[2];
                $notificacion->acuerdo_marco = $data[3];
                $notificacion->tipo_entidad_1 = $data[5];
                $notificacion->entidad_1 = $data[6];
                $notificacion->subtitulo = $data[7];
                $notificacion->fecha_envio = $data[8];
                $notificacion->tipo_entidad_2 = $data[9];
                $notificacion->entidad_2 = $data[10];
                $notificacion->asunto = $data[11];
                $notificacion->mensaje = $data[12];
                $notificacion->denominacion_documento = $data[13];
                $notificacion->documento_adjunto = $data[14];
                $enlace = explode("¯", $data[15]);
                $notificacion->enlace = $enlace[1] . $enlace[0];
                $continuar = false;
            }
        } while ($continuar);
        return response()->json(array('notificacion' => $notificacion, 'tipo' => 'success', 'actualizar' => $actualizar), 200);
    }

    public function actualizarLista($tipo)
    {
        $empresas = Empresa::orderBy('id', 'desc')->get();
        $continuar = false;
        if ($tipo == 1) {
            $usuario = Auth::user();
        } else {
            $usuario = User::find(24);
        }
        LogActividad::registrar($usuario,  $this->nombreFormulario, 8);

        foreach ($empresas as $empresa) {
            do {
                $portal = new PeruComprasHelper();
                $portal->login($empresa, 3); // ealvarez 2
                $respuesta = explode("¯", $portal->enviarData('^^^^' . Carbon::now()->endOfMonth()->addMonths(-6)->toDateString() . '^' . Carbon::now()->endOfMonth()->toDateString() . '^0^', 'https://www.catalogos.perucompras.gob.pe/Notificacion/consultaNotificaciones'));
                if (count($respuesta) == 1) {
                    $continuar = true;
                } else {
                    $filas = explode("¬", $respuesta[1]);
                    foreach ($filas as $fila) {
                        $data = explode("^", $fila);
                        if ($data[0] == "") {
                            continue;
                        }
                        $notificacion = Notificacion::find($data[0]);
                        if ($notificacion == null) {
                            $notificacion = new Notificacion();
                            $notificacion->id = $data[0];
                            $notificacion->id_empresa = $empresa->id;
                            $pos = strpos($data[1], '-');
                            $notificacion->emitido_por = EntidadHelper::obtenerIdPorRuc(substr($data[1], 0, $pos), substr($data[1], $pos + 1), null);
                            $pos = strpos($data[2], '-');
                            $notificacion->destinatario = EntidadHelper::obtenerIdPorRuc(substr($data[2], 0, $pos), substr($data[2], $pos + 1), null);
                            $pos = strpos($data[3], ' ');
                            $notificacion->id_acuerdo_marco = AcuerdoMarco::where('descripcion', substr($data[3], 0, $pos))->first()->id;
                            $notificacion->orden_compra = $data[4];
                        }
                        $notificacion->asunto = $data[5];
                        $notificacion->estado = $data[6];
                        $notificacion->fecha = Carbon::createFromFormat('d/m/Y H:i:s', $data[7])->toDateTimeString();
                        $notificacion->plazo = $data[8] == "" ? null : $data[8];

                        $notificacion->save();
                    }
                    $continuar = false;
                }
            } while ($continuar);
        }
        $fechaDescarga = FechaDescargaNotificacion::find(1);
            $fechaDescarga->fecha_descarga = new Carbon();
        $fechaDescarga->save();

        return response()->json(array('mensaje' => 'Se ha actualizado la lista', 'tipo' => 'success'), 200);
    }
}
