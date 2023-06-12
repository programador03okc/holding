<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use App\Helpers\mgcp\EntidadHelper;
use App\Helpers\mgcp\OrdenCompraAmHelper;
use App\Helpers\mgcp\OrdenCompraPublicaHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\FechaDescargaOcAm;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Estado;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaDetalle;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrdenCompraAmController extends Controller {
    
    /*public function lista() {
        return view('mgcp.usuario.notificacion');
    }

    public function dataLista(Request $request) {
        $data = Notificacion::where('id_usuario',Auth::user()->id)->select(['id','mensaje','fecha','url','leido']);
        return datatables($data)->rawColumns(['mensaje'])->toJson();
    }*/

    public function obtenerProductos(Request $request)
    {
        $helper = new OrdenCompraPublicaHelper($request->idOrden,OrdenCompraAm::find($request->idOrden)->id_entidad);
        $helper->procesar(); //Si ya existe no hará nada
        $detalles=OrdenCompraPublicaDetalle::with('producto','producto.categoria')->where('id_orden_compra',$request->idOrden)->orderBy('id','asc')->get();
        return response()->json(array('tipo' => 'success', 'mensaje' =>'Ok','detalles'=>$detalles));
    }

    public function obtenerFechaDescargaEmpresa(Request $request)
    {
        $ultimaDescarga = new Carbon(FechaDescargaOcAm::find($request->idEmpresa)->fecha_descarga);
        return $ultimaDescarga->format('d-m-Y g:i A') . '<small class="help-block">(' . $ultimaDescarga->diffForHumans() . ')</small>';
    }

    public function actualizarFechaDescargaEmpresa(Request $request)
    {
        $descarga = FechaDescargaOcAm::find($request->idEmpresa);
        $descarga->fecha_descarga = new Carbon();
        $descarga->save();
        return $descarga->fecha_descarga->format('d-m-Y g:i A') . '<small class="help-block">(' . $descarga->fecha_descarga->diffForHumans() . ')</small>';
    }

    public function descargarDesdePortal(Request $request)
    {
        set_time_limit(240);
        $empresa = Empresa::find($request->idEmpresa);
        $catalogo = Catalogo::find($request->idCatalogo);
        $acuerdo = $catalogo->acuerdoMarco;
        LogActividad::registrar(Auth::user(), 'Lista de órdenes de compra propias', 8, null, null, null, 'Empresa: ' . $empresa->empresa . ', catálogo: ' . $catalogo->descripcion);
        
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 2, false)) {
            return response()->json(array('mensaje' => 'Error al iniciar sesión', 'tipo' => 'danger'), 200);
        }

        $dataEnviar['N_Acuerdo'] = $acuerdo->id_pc;
        $dataEnviar['N_Catalogo'] = $catalogo->id_pc;
        $dataEnviar['N_SoloConPagoPend'] = 0;
        $filas = json_decode($portal->enviarData($dataEnviar, "https://www.catalogos.perucompras.gob.pe/OrdenCompra/consulta"));
        
        foreach ($filas->pLista as $fila) {
            $orden = OrdenCompraAm::find($fila->N_OrdenCompra);
            $nuevaOc = false;
            if ($orden == null) {
                $nuevaOc = true;
                $orden = new OrdenCompraAm;
                $orden->id = $fila->N_OrdenCompra;
                $orden->id_empresa = $empresa->id;
                $orden->id_entidad = EntidadHelper::obtenerIdPorNombre($fila->C_Entidad);
                $orden->monto_total = $fila->N_Total;
                $orden->eliminado = false;
                $orden->despachada = false;
                $orden->id_tipo = ($fila->C_Procedimiento == "COMPRA ORDINARIA" ? 1 : 2);
                $orden->id_oportunidad = null;

                $orden->id_etapa = 1;
                $orden->conformidad = false;
                $orden->cobrado = false;
                $orden->id_corporativo = 0;
            }
            $orden->id_catalogo = $catalogo->id;

            if ($nuevaOc == true || $orden->estado_oc = !$fila->C_EstadoOrden || in_array($orden->estado_oc, ['RECHAZADA', 'PUBLICADA', 'PAGADA', 'RESUELTA']) == false) {
                $insistir = true;
                while ($insistir) {
                    $insistir = OrdenCompraAmHelper::obtenerDetallesPortal($portal, $orden);
                    if ($insistir) {
                        $portal->login($empresa, 1, false);
                    }
                }
            }
			
			////validar esta seccion
			/*
            if ($nuevaOc == true || $orden->estado_oc = !$fila->C_EstadoOrden || (Estado::where('id_oc', $orden->id)->count() == 0)) {
                $insistir = true;
                while ($insistir) {
                    $insistir = OrdenCompraAmHelper::registrarEstadosPortal($portal, $orden);
                    if ($insistir) {
                        $portal->login($empresa, 1, false);
                    }
                }
            }
			*/

            $orden->orden_am = $fila->C_OrdenCompra;
            $orden->estado_oc = $fila->C_EstadoOrden;
            $orden->paquete = ($fila->C_TipoContratacion == 'COMPRA INDIVIDUAL' ? 0 : 1);
            $orden->fecha_estado = Carbon::createFromFormat('d/m/Y', $fila->C_FechaEstado)->toDateTimeString();
            $orden->url_oc_fisica = 'https://saeusceprod01.blob.core.windows.net/contentidad/uploads/' . $fila->C_RutaPdfOC;

            if ($orden->estado_oc == 'PUBLICADA') {
                $orden->fecha_publicacion = Carbon::createFromFormat('d/m/Y', $fila->C_FechaEstado)->toDateString();
            }

            $orden->save();
        }
        return response()->json(array('mensaje' => 'Descargado', 'tipo' => 'success'), 200);
    }

    /*public function obtenerDetallesParaDescargarOc()
    {
        $empresas = Empresa::orderBy('id', 'asc')->get();
        $tabla = '<table>';
        foreach ($empresas as $empresa) {
            $catalogos = Catalogo::obtenerCatalogosPorEmpresa($empresa->id);
            $ultimaDescarga = new Carbon(FechaDescargaOcAm::find($empresa->id)->fecha_descarga);
            $tabla .= '<tr>';
            $tabla .=  '<td class="empresa">' . $empresa->empresa . '</td>';
            $tabla .=  '<td class="text-center fecha">';
            $tabla .= $ultimaDescarga->format('d-m-Y g:i A') . '<p class="help-block">(' . $ultimaDescarga->diffForHumans() . ')</p>';
            $tabla .=  '</td>';
            $tabla .=  '<td class="text-center"><input type="checkbox" checked>';
            foreach ($catalogos as $catalogo) {
                $tabla .=  '<input type="hidden" class="pendiente" data-empresa="' . $empresa->id . '" data-catalogo="' . $catalogo->id . '" value="">';
            }
            $tabla .=  '<td class="resultado text-center"></td>';
            $tabla .=  '</td>';
            $tabla .=  '</tr>';
        }
        $tabla .= '</table';
        return $tabla;
    }*/
}
