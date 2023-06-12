<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\User;

use App\Helpers\mgcp\Exportar\OrdenCompraPropiaExport;
use App\Helpers\mgcp\OrdenCompraPublicaHelper;
use App\Models\Contabilidad\Contribuyente;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\FlagEstado;
use App\Models\mgcp\AcuerdoMarco\Provincia;
use App\Models\mgcp\CuadroCosto\EstadoAprobacion;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OcProcesada;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Etapa;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaDetalle;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class OrdenCompraPropiaController extends Controller
{
    private $nombreFormulario = 'Órdenes de compra propias';

    public function lista()
    {
        if (!Auth::user()->tieneRol(31)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $etapas = Etapa::orderBy('id', 'asc')->get();
        $entidades = Entidad::whereRaw('id IN (SELECT id_entidad FROM mgcp_acuerdo_marco.oc_propias)')->orderBy('nombre', 'asc')->get();
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->orderBy('id', 'asc')->get();
        $corporativos = User::whereRaw('(activo=true AND id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE (id_rol=8 OR id_rol=49))) OR id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado=false)')->orderBy('name', 'asc')->withTrashed()->get(); //Corporativo::orderBy('id', 'asc')->get();
        $acuerdos = AcuerdoMarco::orderBy('descripcion', 'asc')->get();
        $transportistas = Contribuyente::whereRaw('id_contribuyente IN (SELECT transportistas.id_contribuyente FROM contabilidad.transportistas)')->orderBy('razon_social', 'asc')->get();
        $estadosOc = DB::select("SELECT DISTINCT estado_oc FROM mgcp_ordenes_compra.oc_propias_view ORDER BY estado_oc");
        $estadosEntrega = DB::select("SELECT DISTINCT estado_entrega FROM mgcp_acuerdo_marco.oc_propias ORDER BY estado_entrega");
        $estadosCuadro = EstadoAprobacion::orderBy('id', 'asc')->get();
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.oc_propias 
                        INNER JOIN mgcp_acuerdo_marco.oc_publica_detalles ON oc_publica_detalles.id_orden_compra = oc_propias.id
                        INNER JOIN mgcp_acuerdo_marco.productos_am ON productos_am.id = id_producto ORDER BY marca");
        $usuarios = User::whereRaw('id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE (id_rol=8 OR id_rol=49)) OR id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado=false)')->orderBy('name', 'asc')->withTrashed()->get();
        $flags = FlagEstado::orderBy('orden', 'asc')->get();
        //$claseCatalogo = new Catalogo();
        return view('mgcp.orden-compra.propia.lista', get_defined_vars());
    }

    /*public function descargarOc()
    {
        $ordenes = DB::select("SELECT oc_propias.id, id_entidad,id_orden_compra FROM mgcp_acuerdo_marco.oc_propias
        LEFT OUTER JOIN mgcp_acuerdo_marco.oc_publica_detalles ON id_orden_compra=oc_propias.id
        WHERE id_orden_compra IS NULL AND id_catalogo NOT IN (21,19,8) AND oc_propias.id NOT IN 
        (SELECT descarga_oc_publica_fallidas.id_oc FROM mgcp_acuerdo_marco.descarga_oc_publica_fallidas)
        AND oc_propias.id NOT IN (SELECT oc_procesadas.id FROM mgcp_acuerdo_marco.oc_procesadas)");
        $limite = 100;
        $contador = 1;
        foreach ($ordenes as $orden) {
            if ($contador <= $limite) {
                $helper = new OrdenCompraPublicaHelper($orden->id, $orden->id_entidad);
                $helper->procesar();
                echo "Procesado: " . $orden->id . '<br>';
                $procesada = new OcProcesada();
                $procesada->id = $orden->id;
                $procesada->save();
                $contador++;
            } else {
                die("FIN");
            }
        }
    }*/

    public function dataLista(Request $request)
    {
        $this->actualizarFiltros($request);
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables(OrdenCompraPropiaView::listar($request))->rawColumns(['entidad.semaforo'])->toJson();
    }

    public function aplicarFiltroIndicador($tipo, $anio)
    {
        session(['ocFiltroFechaPublicacionDesde' => '01-01-' . $anio]);
        session(['ocFiltroFechaPublicacionHasta' => '31-12-' . $anio]);

        Session::forget('ocFiltroEmpresa');
        Session::forget('ocFiltroMarca');
        Session::forget('ocFiltroEntidad');
        Session::forget('ocFiltroEstadoOc');
        Session::forget('ocFiltroEstadoCuadro');
        Session::forget('ocFiltroEstadoEntrega');
        Session::forget('ocFiltroEtapaAdq');
        Session::forget('ocFiltroConformidad');
        Session::forget('ocFiltroCobrado');
        Session::forget('ocFiltroTipo');
        Session::forget('ocFiltroAm');
        Session::forget('ocFiltroCorporativo');
        Session::forget('ocFiltroFechaEstadoDesde');
        Session::forget('ocFiltroFechaEstadoHasta');
        Session::forget('ocFiltroFechaEntregaDesde');
        Session::forget('ocFiltroFechaEntregaHasta');
        Session::forget('ocFiltroSolAprob24h');
        Session::forget('ocFiltroSinCuadro');

        switch ($tipo) {
            case 1: //Con sol. aprob. dsps. 24h
                session(['ocFiltroSolAprob24h' => true]);
                break;
            case 2: //O/C sin cuadro
                $estadosOc = DB::select("SELECT DISTINCT estado_oc FROM mgcp_ordenes_compra.oc_propias_view ORDER BY estado_oc");
                $estadosAplicar = [];
                foreach ($estadosOc as $estado) {
                    if ($estado->estado_oc != 'RECHAZADA') {
                        $estadosAplicar[] = $estado->estado_oc;
                    }
                }
                session(['ocFiltroEstadoOc' => $estadosAplicar]);
                session(['ocFiltroSinCuadro' => true]);
                break;
        }
        return redirect()->route('mgcp.ordenes-compra.propias.lista');
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkEmpresa == 'on') {
            if ($request->selectEmpresa != null && count($request->selectEmpresa) > 0) {
                session(['ocFiltroEmpresa' => $request->selectEmpresa]);
            } else {
                session(['ocFiltroEmpresa' => [0]]); //Para no obtener resultados si no se selecciona una empresa
            }
        } else {
            $request->session()->forget('ocFiltroEmpresa');
        }

        if ($request->chkMarca == 'on') {
            if ($request->selectMarca != null && count($request->selectMarca) > 0) {
                session(['ocFiltroMarca' => $request->selectMarca]);
            } else {
                session(['ocFiltroMarca' => [0]]); //Para no obtener resultados si no se selecciona una marca
            }
        } else {
            $request->session()->forget('ocFiltroMarca');
        }

        if ($request->chkEntidad == 'on') {
            session(['ocFiltroEntidad' => $request->selectEntidad]);
        } else {
            $request->session()->forget('ocFiltroEntidad');
        }

        /*if ($request->chkEstadoOc == 'on') {
            session(['ocFiltroEstadoOc' => $request->estadoOc]);
        } else {
            $request->session()->forget('ocFiltroEstadoOc');
        }*/

        if ($request->chkEstadoOc == 'on') {
            if ($request->estadoOc != null && count($request->estadoOc) > 0) {
                session(['ocFiltroEstadoOc' => $request->estadoOc]);
            } else {
                session(['ocFiltroEstadoOc' => [0]]); //Para no obtener resultados si no se selecciona una marca
            }
        } else {
            $request->session()->forget('ocFiltroEstadoOc');
        }

        if ($request->chkEstadoCuadro == 'on') {
            session(['ocFiltroEstadoCuadro' => $request->estadoCuadro]);
        } else {
            $request->session()->forget('ocFiltroEstadoCuadro');
        }

        if ($request->chkEstadoEntrega == 'on') {
            session(['ocFiltroEstadoEntrega' => $request->estadoEntrega]);
        } else {
            $request->session()->forget('ocFiltroEstadoEntrega');
        }

        if ($request->chkEtapaAdq == 'on') {
            session(['ocFiltroEtapaAdq' => $request->etapaAdq]);
        } else {
            $request->session()->forget('ocFiltroEtapaAdq');
        }

        if ($request->chkConformidad == 'on') {
            session(['ocFiltroConformidad' => $request->conformidad]);
        } else {
            $request->session()->forget('ocFiltroConformidad');
        }

        if ($request->chkCobrado == 'on') {
            session(['ocFiltroCobrado' => $request->cobrado]);
        } else {
            $request->session()->forget('ocFiltroCobrado');
        }

        if ($request->chkTipo == 'on') {
            session(['ocFiltroTipo' => $request->tipoOc]);
        } else {
            $request->session()->forget('ocFiltroTipo');
        }

        if ($request->chkAm == 'on') {
            if ($request->acuedoMarco != null && count($request->acuedoMarco) > 0) {
                session(['ocFiltroAm' => $request->acuedoMarco]);
            } else {
                session(['ocFiltroAm' => [0]]); //Para no obtener resultados si no se selecciona una empresa
            }
        } else {
            $request->session()->forget('ocFiltroAm');
        }

        if ($request->chkCorporativo == 'on') {
            session(['ocFiltroCorporativo' => $request->corporativo]);
        } else {
            $request->session()->forget('ocFiltroCorporativo');
        }

        if ($request->chkFechaEstado == 'on') {
            session(['ocFiltroFechaEstadoDesde' => $request->fechaEstadoDesde]);
            session(['ocFiltroFechaEstadoHasta' => $request->fechaEstadoHasta]);
        } else {
            $request->session()->forget('ocFiltroFechaEstadoDesde');
            $request->session()->forget('ocFiltroFechaEstadoHasta');
        }

        if ($request->chkFechaEntrega == 'on') {
            session(['ocFiltroFechaEntregaDesde' => $request->fechaEntregaDesde]);
            session(['ocFiltroFechaEntregaHasta' => $request->fechaEntregaHasta]);
        } else {
            $request->session()->forget('ocFiltroFechaEntregaDesde');
            $request->session()->forget('ocFiltroFechaEntregaHasta');
        }

        if ($request->chkFechaPublicacion == 'on') {
            session(['ocFiltroFechaPublicacionDesde' => $request->fechaPublicacionDesde]);
            session(['ocFiltroFechaPublicacionHasta' => $request->fechaPublicacionHasta]);
        } else {
            $request->session()->forget('ocFiltroFechaPublicacionDesde');
            $request->session()->forget('ocFiltroFechaPublicacionHasta');
        }

        if ($request->chkSolAprob24h == 'on') {
            session(['ocFiltroSolAprob24h' => true]);
        } else {
            $request->session()->forget('ocFiltroSolAprob24h');
        }

        if ($request->chkSinCuadro == 'on') {
            session(['ocFiltroSinCuadro' => true]);
        } else {
            $request->session()->forget('ocFiltroSinCuadro');
        }

        if ($request->chkFlagEstado == 'on') {
            session(['ocFiltroFlagEstado' => $request->flagEstado]);
        } else {
            $request->session()->forget('ocFiltroFlagEstado');
        }

        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se han aplicado los filtros', 'sesiones' => $request->session()), 200);
    }

    public function exportarLista(Request $request)
    {
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 5);
        $cabecera = OrdenCompraPropiaView::listar($request, true)->orderBy('fecha_publicacion', 'desc')->orderBy('id_empresa', 'asc')->get();
        $exportar = new OrdenCompraPropiaExport();
        $exportar->generarHojaLista($cabecera);
        
        if ($request->incluirProductos == 1) {
            $detalles = OrdenCompraPropiaView::listar($request)
                ->leftJoin('mgcp_acuerdo_marco.oc_publica_detalles', 'id_orden_compra', 'oc_propias_view.id')
                ->leftJoin('mgcp_acuerdo_marco.productos_am', 'id_producto', 'productos_am.id')
                ->leftJoin('mgcp_acuerdo_marco.categorias', 'id_categoria', 'categorias.id')
                ->leftJoin('mgcp_acuerdo_marco.catalogos', 'categorias.id_catalogo', 'catalogos.id')
                ->leftJoin('mgcp_acuerdo_marco.acuerdo_marco', 'catalogos.id_acuerdo_marco', 'acuerdo_marco.id')
                ->where('tipo', 'am')->orderBy('fecha_publicacion', 'desc')
                ->orderBy('id_empresa', 'asc')->orderBy('nro_orden', 'asc')
                ->select([
                    'oc_propias_view.*', 'productos_am.descripcion AS descripcion_producto', 'catalogos.descripcion AS catalogo',
                    'categorias.descripcion AS categoria', 'marca', 'modelo', 'part_no', 'cantidad', 'precio_unitario'
                ]);
            if ($request->session()->has('ocFiltroMarca')) {
                $detalles = $detalles->whereIn('marca', session('ocFiltroMarca'));
            }
            $exportar->generarHojaDetalles($detalles->get());
        }
        $exportar->descargarArchivo();
    }

    public function obtenerInformacionAdicional(Request $request)
    {
        /*No se registra esta actividad en el log debido a que este acceso no requiere login para que el sistema Agil pueda acceder*/
        header('Access-Control-Allow-Origin: *');
        $orden = OrdenCompraPropiaView::where('id', $request->id)->where('tipo', $request->tipo)->first();
        return response()->json(array('tipo' => 'success', 'lugar_entrega' => $orden->lugar_entrega, 'archivos' => $orden->archivos));
    }

    public function cambiarContacto(Request $request)
    {
        if (!Auth::user()->tieneRol(60)) { //Permiso relacionado a gestionar contactos
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para realizar el cambio'));
        }
        if ($request->tipoOrden == 'am') {
            $orden = OrdenCompraAm::find($request->idOrden);
        } else {
            $orden = OrdenCompraDirecta::find($request->idOrden);
        }

        $orden->id_contacto = $request->idContacto;
        $orden->save();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha cambiado el contacto de la O/C'));
    }

    public function actualizarCampo(Request $request)
    {
        $campo = $request->campo;
        $valor = $request->valor;
        if (in_array($campo, array('fecha_entrega', 'occ', 'orden_compra', 'siaf', 'codigo_gasto', 'factura', 'guia', 'id_etapa', 'id_corporativo', 'conformidad', 'cobrado', 'fecha_guia'))) {
            if ($request->tipoOrden == 'am') {
                $orden = OrdenCompraAm::find($request->id);
                $nroOrden = $orden->orden_am;
            } else {
                $orden = OrdenCompraDirecta::find($request->id);
                $nroOrden = $orden->orden_am;
            }

            switch ($campo) {
                case 'id_etapa':
                    $valorAnterior = Etapa::find($orden->$campo)->etapa;
                    $nuevoValor = Etapa::find($valor)->etapa;
                break;
                case 'id_corporativo':
                    $valorAnterior = ($orden->$campo) ? User::find($orden->$campo)->nombre_corto : '';
                    $nuevoValor = ($valor != 0) ? User::find($valor)->nombre_corto: 'No asignado';
                break;
                default:
                    $valorAnterior = $orden->$campo;
                    $nuevoValor = $valor;
                break;
            }

            $dataAnterior[$campo] = $valorAnterior ?? '';
            $dataNueva[$campo] = $nuevoValor ?? '';
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $orden->getTable(), $dataAnterior, $dataNueva, 'O/C: '.$nroOrden);

                $orden->$campo = $valor;
                $orden->id_ultimo_usuario = Auth::user()->id;
            $orden->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Actualizado'));
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No se pudo guardar la celda marcada en rojo. Elimine la X para volver a intentarlo'));
        }
    }

    /*public function cambiarDespacho(Request $request)
    {
        if (!Auth::user()->tieneRol(51)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para realizar el cambio'));
        }
        if ($request->tipo == 'am') {
            $orden = OrdenCompraAm::find($request->id);
        } else {
            $orden = OrdenCompraDirecta::find($request->id);
        }
        $orden->despachada = $request->despachada == 1 ? true : false;
        $orden->id_ultimo_usuario=Auth::user()->id;
        $orden->save();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se realizó el cambio', 'despacho' => $orden->despachada));
    }*/

    public function vincularOportunidad(Request $request)
    {
        $oportunidad = Oportunidad::find($request->idOportunidad);
        if ($oportunidad == null || $oportunidad->eliminado) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'La oportunidad ingresada no existe o fue eliminada. Seleccione otra oportunidad e intentelo de nuevo.'));
        }
        $oportunidadAsignada = OrdenCompraPropiaView::where('id_oportunidad', $request->idOportunidad)->first();
        if ($oportunidadAsignada != null) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'La oportunidad seleccionada ya se asignó a la orden ' . $oportunidadAsignada->nro_orden . '. Seleccione otra oportunidad e intentelo de nuevo.'));
        }
        DB::beginTransaction();

        try {
            $orden = $request->tipo == 'am' ? OrdenCompraAm::find($request->idOc) : OrdenCompraDirecta::find($request->idOc);
                $orden->id_corporativo = $oportunidad->id_responsable;
                $orden->id_oportunidad = $request->idOportunidad;
            $orden->save();
            LogActividad::registrar(Auth::user(), 'Órdenes de compra propias', 6, null, null, 'Oportunidad: ' . $oportunidad->codigo_oportunidad . ', O/C: ' . ($request->tipo == 'am' ? $orden->orden_am : $orden->nro_orden));
            
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha asignado la oportunidad ' . $oportunidad->codigo_oportunidad . ' a la orden ' . $orden->orden_am . '. El sistema lo redireccionará al cuadro de costos creado...'));
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Hubo un problema al asignar la oportunidad. Por favor inténtelo de nuevo'));
        }
    }
}
