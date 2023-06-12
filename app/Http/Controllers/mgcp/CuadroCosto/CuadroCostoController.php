<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use App\Helpers\mgcp\CuadroCosto\CuadroCostoHelper;
use App\Helpers\mgcp\CuadroCosto\Exportar\ListaCuadroCostoExport;
use App\Helpers\mgcp\CuadroCosto\RequerimientoHelper;
use App\Helpers\mgcp\OrdenCompraAmHelper;
use App\Helpers\mgcp\OrdenCompraDirectaHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\mgcp\CuadroCosto\CuadroFinalizado;
use App\Mail\mgcp\CuadroCosto\HojaTransformacion;
use App\Mail\mgcp\CuadroCosto\OrdenDespacho;
use App\Mail\mgcp\CuadroCosto\OrdenServicio;
use App\Models\Almacen\Requerimiento;
use App\Models\mgcp\CuadroCosto\AmFilaTipo;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\User;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CcVenta;
use App\Models\mgcp\CuadroCosto\CcAm;
use App\Models\mgcp\CuadroCosto\CcBs;
use App\Models\mgcp\CuadroCosto\CcGg;
use App\Models\mgcp\CuadroCosto\CcVentaFila;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcBsFila;
use App\Models\mgcp\CuadroCosto\CcGgFila;
use App\Models\mgcp\CuadroCosto\CategoriaGasto;
use App\Models\mgcp\CuadroCosto\Proveedor;
use App\Models\mgcp\CuadroCosto\DetalleProceso;
use App\Models\mgcp\CuadroCosto\CcSolicitud;
use App\Models\mgcp\CuadroCosto\Responsable;
use App\Models\mgcp\CuadroCosto\CcGasto;
use App\Models\mgcp\CuadroCosto\CcTipoSolicitud;
use App\Models\mgcp\CuadroCosto\CondicionCredito;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\CuadroCosto\EstadoAprobacion;
use App\Models\mgcp\CuadroCosto\Gasto;
use App\Models\mgcp\CuadroCosto\OrigenCosteo;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\Presupuestos\CentroCostoNivelView;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use stdClass;

class CuadroCostoController extends Controller
{
    private $nombreFormulario = 'Cuadros de presupuesto';
    private $camposNoEditables = array('id', 'id_oportunidad', 'aprobado');

    public function replicarCuadroCostoTotal()
    {
        $helper = new RequerimientoHelper();
        $cuadros = CuadroCostoView::whereRaw("id_estado_aprobacion IN (3,4,5) AND EXTRACT(year FROM fecha_creacion)=2021 AND EXTRACT(month FROM fecha_creacion)>7")->get();
        foreach ($cuadros as $cuadro) {
            $helper->replicarPorCuadroCosto($cuadro->id_oportunidad);
        }
        die("FIN de replicar");
    }

    public function replicarCuadroCostoId($id)
    {
        $helper = new RequerimientoHelper();
        $helper->replicarPorCuadroCosto($id);
        die("FIN de replicar");
    }

    public function lista()
    {
        if (!Auth::user()->tieneRol(54)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $estados = EstadoAprobacion::orderBy('id', 'asc')->get();
        $responsablesOportunidad = User::whereRaw('id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades)')->orderBy('name', 'asc')->withTrashed()->get();
        $responsablesAprobacion = User::whereRaw('id IN (SELECT enviada_a FROM mgcp_cuadro_costos.cc_solicitudes)')->orderBy('name', 'asc')->withTrashed()->get();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.cuadro-costo.lista')->with(compact('estados', 'responsablesOportunidad', 'responsablesAprobacion'));
    }

    public function aplicarFiltroIndicador($tipo, $anio)
    {
        session(['ccFiltroFechaCreacionDesde' => '01-01-' . $anio]);
        session(['ccFiltroFechaCreacionHasta' => '31-12-' . $anio]);

        Session::forget('ccFiltroEstado');
        Session::forget('ccFiltroTipo');
        Session::forget('ccFiltroResponsableOportunidad');
        Session::forget('ccFiltroResponsableAprobacion');

        switch ($tipo) {
            case 1: //Pendientes de aprobar
                session(['ccFiltroEstado' => 2]);
                break;
            case 2: //Pendientes de regularizar
                session(['ccFiltroEstado' => 5]);
                break;
        }
        return redirect()->route('mgcp.cuadro-costos.lista');
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkFechaCreacion == 'on') {
            session(['ccFiltroFechaCreacionDesde' => $request->fechaCreacionDesde]);
            session(['ccFiltroFechaCreacionHasta' => $request->fechaCreacionHasta]);
        } else {
            $request->session()->forget('ccFiltroFechaCreacionDesde');
            $request->session()->forget('ccFiltroFechaCreacionHasta');
        }

        if ($request->chkEstado == 'on') {
            session(['ccFiltroEstado' => $request->selectEstado]);
        } else {
            $request->session()->forget('ccFiltroEstado');
        }

        if ($request->chkTipo == 'on') {
            session(['ccFiltroTipo' => $request->selectTipo]);
        } else {
            $request->session()->forget('ccFiltroTipo');
        }

        if ($request->chkResponsableOportunidad == 'on') {
            session(['ccFiltroResponsableOportunidad' => $request->selectResponsableOportunidad]);
        } else {
            $request->session()->forget('ccFiltroResponsableOportunidad');
        }

        if ($request->chkResponsableAprobacion == 'on') {
            session(['ccFiltroResponsableAprobacion' => $request->selectResponsableAprobacion]);
        } else {
            $request->session()->forget('ccFiltroResponsableAprobacion');
        }

        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se han aplicado los filtros'), 200);
    }

    public function dataLista(Request $request)
    {
        $this->actualizarFiltros($request);
        $cuadros = CuadroCostoView::listar($request);
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables($cuadros)->toJson();
    }

    public function ajaxDetalles(Request $request)
    {
        $cuadro = CuadroCosto::find($request->idCuadro);
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad);
        return response()->json($cuadro);
    }

    public function exportarLista(Request $request)
    {
        if (!Auth::user()->tieneRol(54)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 5);
        $data = CuadroCostoView::listar($request)->orderBy('fecha_creacion', 'asc')->get();
        $exportar = new ListaCuadroCostoExport();
        $exportar->exportar($data);
    }

    public function detalles($id)
    {
        $oportunidad = Oportunidad::find($id);
        //$this->enviarHojaTransformacionCorreo($oportunidad);
        //return view('mgcp.cuadro-costo.email.hoja-transformacion')->with(compact('oportunidad'));
        if ($oportunidad == null || $oportunidad->eliminado) {
            return redirect()->route('home');
        }

        if ($oportunidad->id_responsable != Auth::user()->id && !Auth::user()->tieneRol(54)) {
            return view('mgcp.usuario.sin_permiso');
        }

        //Si ya existe el cuadro, no se crea en la función
        $cuadroCosto = CuadroCostoHelper::crearDesdeOportunidad($oportunidad);
        $ultimaSolicitud = CcSolicitud::where('id_cc', $cuadroCosto->id)->orderBy('id', 'desc')->first();
        $tipoEdicion = CuadroCosto::tipoEdicion($cuadroCosto, Auth::user());

        //$ccVenta = CcVenta::find($cuadroCosto->id);
        $ccAm = CcAm::find($cuadroCosto->id);
        $ccBs = CcBs::find($cuadroCosto->id);
        $ccGg = CcGg::find($cuadroCosto->id);

        //$ccVentaFilas = CcVentaFila::where('id_cc_venta', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $ccAmFilas = CcAmFila::where('id_cc_am', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $ccBsFilas = CcBsFila::where('id_cc_bs', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $ccGgFilas = CcGgFila::where('id_cc_gg', $cuadroCosto->id)->orderBy('id', 'asc')->get();
        $categoriasGasto = CategoriaGasto::orderBy('categoria', 'asc')->get();
        $proveedores = Proveedor::orderBy('razon_social', 'asc')->get();
        //$detallesProceso = DetalleProceso::orderBy('detalle', 'asc')->get();
        $corporativos = User::whereRaw('id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE id_rol=8) OR id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado=false)')->orderBy('name', 'asc')->get();
        $responsables = Responsable::where('id_cc', $cuadroCosto->id)->get();
        //$gastosVinculados = CcGasto::where('id_cc', $cuadroCosto->id)->select(['id_gasto'])->get();
        $origenesCosteo = OrigenCosteo::orderBy('id', 'asc')->get();
        $tiposSolicitud = CcTipoSolicitud::orderBy('id', 'asc')->get();
        $rolCompras = Auth::user()->tieneRol(46);
        $tiposFila = AmFilaTipo::get();

        $gastos = Gasto::listar();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null,'CDP: ' . $oportunidad->codigo_oportunidad);
        return view('mgcp.cuadro-costo.detalles')
            ->with(compact('oportunidad', 'cuadroCosto', 'ccAm', 'ccAmFilas', 'origenesCosteo'))
            ->with(compact('ccBs', 'ccGg', 'ccBsFilas', 'ccGgFilas', 'categoriasGasto', 'proveedores'))
            ->with(compact('tipoEdicion', 'ultimaSolicitud',  'tiposSolicitud'))
            ->with(compact('corporativos', 'responsables', 'rolCompras', 'tiposFila'));
    }

    public function actualizarCampo(Request $request)
    {
        if (!Auth::user()->tieneRol(126) && $request->campo == 'tipo_cambio') {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para actualizar el tipo de cambio'), 200);
        }

        $campo = $request->campo;
        $cc = CuadroCosto::find($request->id);
        if ($campo == 'tipo_cambio' || (CuadroCosto::tipoEdicion($cc, Auth::user()) == 'corporativo' && !in_array($request->campo, $this->camposNoEditables))) {
            $nombreCampo = "";

            switch ($request->campo) {
                    /*case 'tipo_cuadro':
                    $nombreCampo = "el tipo de cuadro";
                    break;*/
                case 'tipo_cambio':
                    $nombreCampo = "el tipo de cambio";
                break;
                case 'moneda':
                    $nombreCampo = "la moneda de los subtotales";
                break;
                case 'porcentaje_responsable':
                    $nombreCampo = "el porcentaje del responsable";
                break;
            }
            $dataAnterior[$campo] = $cc->$campo;
            $dataNueva[$campo] = $request->valor;
            $cc->$campo = $request->valor;
            $cc->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $cc->getTable(), $dataAnterior, $dataNueva, 'CDP: '.$cc->oportunidad->codigo_oportunidad);
            return response()->json(array('tipo' => 'success', 'mensaje' => "Se ha actualizado $nombreCampo"), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function actualizarCondicionCredito(Request $request)
    {
        $cuadro = CuadroCosto::find($request->id);
        
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
                $dataAnterior['condicion_credito'] = CondicionCredito::find($cuadro->id_condicion_credito)->descripcion;
                $dataNueva['condicion_credito'] = CondicionCredito::find($request->tipo)->descripcion;
                $cuadro->id_condicion_credito = $request->tipo;
                $cuadro->dato_credito = $request->tipo == 1 ? 0 : $request->dato;
                $dataAnterior['dato_credito'] = $cuadro->dato_credito;
                $dataNueva['dato_credito'] = $request->tipo == 1 ? 0 : $request->dato;
            $cuadro->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $cuadro->getTable(), $dataAnterior, $dataNueva, 'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha actualizado la condición de crédito'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function finalizar(Request $request)
    {
        if (!Auth::user()->tieneRol(46)) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El usuario debe tener el rol de compras para finalizar el cuadro.'), 200);
        }

        $cuadro = CuadroCosto::find($request->idCuadro);
        if ($cuadro == null) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro no existe.'), 200);
        }
        if ($cuadro->estado_aprobacion != 3) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro debe estar aprobado antes de ser finalizado.'), 200);
        }

        DB::beginTransaction();
        try {
                $dataAnterior['estado_aprobacion'] = EstadoAprobacion::find($cuadro->estado_aprobacion)->estado;
                $dataNueva['estado_aprobacion'] = EstadoAprobacion::find(4)->estado;
                $cuadro->estado_aprobacion = 4;
            $cuadro->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $cuadro->getTable(), $dataAnterior, $dataNueva,'CDP: '.$cuadro->oportunidad->codigo_oportunidad);

            //Marcar todas las filas como compradas
            CcAmFila::where('id_cc_am', $cuadro->id)->update(['comprado' => true]);
            CcBsFila::where('id_cc_bs', $cuadro->id)->update(['comprado' => true]);

            OrdenCompraPropiaView::actualizarEstadoCompra($cuadro->oportunidad->ordenCompraPropia, 2);
            //Correo
            $url = route('mgcp.cuadro-costos.detalles', ['id' => $request->idCuadro]);
            $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
            $destinatario = User::find($oportunidad->id_responsable);
            if (config('app.env') != 'testing') {
                //Mail::to(config('app.debug') ? config('global.adminEmail') : $destinatario->email)->send(new CuadroFinalizado($url, $oportunidad, Auth::user()));
                $this->enviarHojaTransformacionCorreo($oportunidad);
            }
            DB::commit();
            return response()->json(array('tipo' => 'success', 'titulo' => 'El cuadro ha sido finalizado', 'texto' => 'La página se actualizará'), 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'titulo' => 'Hubo un problema al procesar su solicitud', 'texto' => 'Por favor intente de nuevo. Mensaje de error: ' . $ex->getMessage()), 200);
        }
    }

    private function enviarHojaTransformacionCorreo($oportunidad)
    {
        if (config('app.debug')) {
            //Mail::to(config('global.adminEmail'))->send(new HojaTransformacion($oportunidad));
        } else {
            //Usuarios para transformación
            $correos = [];
            $correos[] = $oportunidad->responsable->email;
            $usuariosTransformacion = User::obtenerPorRol(131);
            foreach ($usuariosTransformacion as $usuario) {
                $correos[] = $usuario->email;
            }
            //Mail::to($correos)->send(new HojaTransformacion($oportunidad));
        }
    }

    public function obtenerDetallesFilas(Request $request)
    {
        return response()->json(array('tipo' => 'success', 'data' => CuadroCostoHelper::obtenerDetallesFilas($request->id)), 200);
    }

    public function seleccionarCentroCosto(Request $request)
    {
        $cuadro = CuadroCosto::find($request->idCuadro);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            $centroAnterior = CentroCostoNivelView::find($cuadro->id_centro_costo);
            $nuevoCentro = CentroCostoNivelView::find($request->idCentro);

            $dataAnterior['centro_costo'] = !is_null($centroAnterior) ? ($centroAnterior->unidad .' - '.$centroAnterior->division) : '';
            $dataNueva['centro_costo'] = $nuevoCentro->unidad .' - '.$nuevoCentro->division;

            $cuadro->id_centro_costo = $request->idCentro;
            $cuadro->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $cuadro->getTable(), $dataAnterior, $dataNueva, 'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
            $centroCosto = CentroCostoNivelView::find($request->idCentro);
            
            return response()->json(array(
                'mensaje' => 'Se ha seleccionado el centro de costo', 'tipo' => 'success',
                'descripcion' => $centroCosto->unidad . ' - ' . $centroCosto->division . (empty($centroCosto->segmento) ? '' : ' - ' . $centroCosto->segmento)
            ), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function enviarOrdenDespacho(Request $request)
    {
        if (!Auth::user()->tieneRol(133)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No tiene permiso para enviar ordenes de despacho'), 200);
        }
        $cuadro = CuadroCosto::find($request->idCuadro);
        if ($cuadro == null) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro no existe.'), 200);
        }
        if ($cuadro->estado_aprobacion < 3) {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro debe estar aprobado para enviar la orden.'), 200);
        }
        try {

            $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
            $ordenView = $oportunidad->ordenCompraPropia;
            $archivosOc = [];
            //Obtencion de archivos en carpeta temporal
            if ($ordenView != null) {
                if ($ordenView->tipo == 'am') {
                    $archivosOc = OrdenCompraAmHelper::descargarArchivos($ordenView->id);
                } else {
                    $archivosOc = OrdenCompraDirectaHelper::copiarArchivos($ordenView->id);
                }
            }
            //Guardar archivos subidos
            if ($request->hasFile('archivos')) {
                $archivos = $request->file('archivos');
                foreach ($archivos as $archivo) {
                    Storage::putFileAs('mgcp/ordenes-compra/temporal/', $archivo, $archivo->getClientOriginalName());
                    $archivosOc[] = storage_path('app/mgcp/ordenes-compra/temporal/') . $archivo->getClientOriginalName();
                }
            }

            $correos = [];
            if (config('app.debug')) {
                $correos[] = config('global.adminEmail');
            } else {
                $correos[] = $oportunidad->responsable->email;
                /*$usuarios = User::obtenerPorRol(131);
                foreach ($usuarios as $usuario) {
                    $correos[] = $usuario->email;
                }*/
                $usuarios = User::obtenerPorRol(134);
                foreach ($usuarios as $usuario) {
                    $correos[] = $usuario->email;
                }
            }

            //Mail::to($correos)->send(new OrdenDespacho($oportunidad, $request->mensaje, $archivosOc));

            foreach ($archivosOc as $archivo) {
                unlink($archivo);
            }
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha enviado la orden de despacho a ' . count($correos) . (count($correos) == 1 ? ' persona' : ' personas')), 200);
        } catch (Exception $ex) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al procesar su solicitud, por favor intente de nuevo. Mensaje: ' . $ex->getMessage()), 200);
        }
    }
}
