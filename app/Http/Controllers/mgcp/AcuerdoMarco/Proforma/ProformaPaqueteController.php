<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaFiltrosHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPaqueteVistaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPortalHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\EnvioDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Paquete;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\ProductoDetalle;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProformaPaqueteController extends Controller
{
    private $nombreFormulario = 'Proforma de Acuerdo marco por paquete ';
    protected $tipoProforma;

    public function obtenerListaParaEnviarPortal(Request $request)
    {
        set_time_limit(240);
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso para realizar esta acción', 'tipo' => 'error'), 200);
        }
        $proformas = Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos', 'proforma_paquete_productos.proforma_paquete_id', 'proformas_paquete.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles', 'proforma_paquete_producto_id', 'proforma_paquete_productos.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_destinos', 'proforma_paquete_destinos.proforma_paquete_id', 'proformas_paquete.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_envios', 'proforma_paquete_destino_id', 'proforma_paquete_destinos.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_envio_detalles', function ($join) {
                $join->on('proforma_paquete_envio_id', 'proforma_paquete_envios.id');
                $join->on('proforma_paquete_envio_detalles.nro_proforma', 'proforma_paquete_producto_detalles.nro_proforma');
            })
            ->join('mgcp_acuerdo_marco.empresas', 'empresas.id', 'proformas_paquete.id_empresa')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
            ->leftJoin('mgcp_usuarios.users', 'users.id', 'id_ultimo_usuario')
            ->where('seleccionado', true)->where('estado', 'PENDIENTE')->where('tipo', $request->tipoProforma)
            ->select([
                'proformas_paquete.id', 'proformas_paquete.id_empresa', 'moneda_ofertada', 'requiere_flete', DB::raw('false AS restringir'), 'id_ultimo_usuario',
                DB::raw('(SELECT COUNT(DISTINCT pppdet.proforma) FROM mgcp_acuerdo_marco.proformas_paquete AS pp 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_productos AS ppprod ON ppprod.proforma_paquete_id=pp.id 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_producto_detalles AS pppdet ON pppdet.proforma_paquete_producto_id=ppprod.id
                    WHERE pp.id=proformas_paquete.id AND pp.id_empresa=proformas_paquete.id_empresa) AS total_proformas'
                ),
                DB::raw('(SELECT COUNT(DISTINCT pppdet.proforma) FROM mgcp_acuerdo_marco.proformas_paquete AS pp 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_productos AS ppprod ON ppprod.proforma_paquete_id=pp.id 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_producto_detalles AS pppdet ON pppdet.proforma_paquete_producto_id=ppprod.id
                    WHERE pp.id=proformas_paquete.id AND pp.id_empresa=proformas_paquete.id_empresa AND seleccionado=true AND pppdet.nro_proforma IN 
                    (SELECT env.nro_proforma FROM mgcp_acuerdo_marco.proforma_paquete_producto_detalles pppdet
                        INNER JOIN mgcp_acuerdo_marco.proforma_paquete_envio_detalles env ON env.nro_proforma=pppdet.nro_proforma
                        WHERE env.nro_proforma=pppdet.nro_proforma AND env.costo_envio_publicar IS NOT NULL)
                    ) AS total_proformas_seleccionadas'),
                'requerimiento', 'proforma', 'empresas.empresa', 'lugar_entrega', 'marca', 'modelo', 'part_no', 'precio_publicar',
                'costo_envio_publicar', 'users.nombre_corto', 'fecha_limite', 'seleccionado'
            ])->orderBy('requerimiento')->orderBy('proformas_paquete.id_empresa')->orderBy('proforma')->get();

        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 15, null, null, null, ($request->tipoProforma == 1 ? 'Compra ordinaria' : 'Gran compra'),);
        return response()->json($proformas, 200);
    }

    public function testPortal()
    {
        set_time_limit(2000);
        $proformas = Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos', 'proforma_paquete_productos.proforma_paquete_id', 'proformas_paquete.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles', 'proforma_paquete_producto_id', 'proforma_paquete_productos.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_destinos', 'proforma_paquete_destinos.proforma_paquete_id', 'proformas_paquete.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_envios', 'proforma_paquete_destino_id', 'proforma_paquete_destinos.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_envio_detalles', function ($join) {
                $join->on('proforma_paquete_envio_id', 'proforma_paquete_envios.id');
                $join->on('proforma_paquete_envio_detalles.nro_proforma', 'proforma_paquete_producto_detalles.nro_proforma');
            })
            ->join('mgcp_acuerdo_marco.empresas', 'empresas.id', 'id_empresa')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
            ->leftJoin('mgcp_usuarios.users', 'users.id', 'id_ultimo_usuario')
            ->where('seleccionado', true)->where('estado', 'PENDIENTE')->where('tipo', 1)
            ->select([
                'proformas_paquete.id', 'id_empresa', 'moneda_ofertada', 'requiere_flete', DB::raw('false AS restringir'), 'id_ultimo_usuario',
                DB::raw('(SELECT COUNT(DISTINCT pppdet.proforma) FROM mgcp_acuerdo_marco.proformas_paquete AS pp 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_productos AS ppprod ON ppprod.proforma_paquete_id=pp.id 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_producto_detalles AS pppdet ON pppdet.proforma_paquete_producto_id=ppprod.id
                    WHERE pp.id=proformas_paquete.id AND pp.id_empresa=proformas_paquete.id_empresa) AS total_proformas'
                ),
                DB::raw('(SELECT COUNT(DISTINCT pppdet.proforma) FROM mgcp_acuerdo_marco.proformas_paquete AS pp 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_productos AS ppprod ON ppprod.proforma_paquete_id=pp.id 
                    JOIN mgcp_acuerdo_marco.proforma_paquete_producto_detalles AS pppdet ON pppdet.proforma_paquete_producto_id=ppprod.id
                    WHERE pp.id=proformas_paquete.id AND pp.id_empresa=proformas_paquete.id_empresa AND seleccionado=true AND pppdet.nro_proforma IN 
                    (SELECT env.nro_proforma FROM mgcp_acuerdo_marco.proforma_paquete_producto_detalles pppdet
                        INNER JOIN mgcp_acuerdo_marco.proforma_paquete_envio_detalles env ON env.nro_proforma=pppdet.nro_proforma
                        WHERE env.nro_proforma=pppdet.nro_proforma AND env.costo_envio_publicar IS NOT NULL)
                    ) AS total_proformas_seleccionadas'),
                'requerimiento', 'proforma', 'empresas.empresa', 'lugar_entrega', 'marca', 'modelo', 'part_no', 'precio_publicar',
                'costo_envio_publicar', 'users.nombre_corto', 'fecha_limite', 'seleccionado'
            ])->orderBy('requerimiento')->orderBy('id_empresa')->orderBy('proforma')->get();
            return response()->json($proformas, 200);
    }

    public function enviarCotizacionPortal(Request $request)
    {
        $requerimiento = Paquete::find($request->idRequerimiento); 
        if ($requerimiento->estado != 'PENDIENTE') {
            return response()->json(array('mensaje' => 'Ya enviada', 'tipo' => 'success'), 200);
        }

        $empresa = Empresa::find($requerimiento->id_empresa);
        $portalHelper = new PeruComprasHelper();
        if (!$portalHelper->login($empresa, 3)) { // ealvarez 2
            return response()->json(array('mensaje' => 'Error al iniciar sesión', 'tipo' => 'danger'), 200);
        } else {
            $resultado = ProformaPortalHelper::proformaPaqueteEnviarCotizacion($portalHelper, $requerimiento);//$proforma->restringir ? ProformaHelper::restringir($portalHelper, $proforma) : 
            if ($resultado->mensaje_rpta == 'Ejecutado Correctamente') {
                $requerimiento->estado ='COTIZADA';
                $requerimiento->save();
                return response()->json(array('mensaje' => 'Enviada', 'tipo' => 'success'), 200);
            } else {
                return response()->json(array('mensaje' => 'Error de Perú Compras: ' . $resultado->mensaje_rpta, 'tipo' => 'danger'), 200);
            }
        }
    }

    public function deshacerCotizacion(Request $request)
    {
    }

    public function obtenerProformas(Request $request)
    {
        /*Se obtienen los requerimientos de acuerdo a los filtros seleccionados*/
        //$this->actualizarFiltros($request);
        ProformaFiltrosHelper::actualizar($request);
        $helper = new ProformaPaqueteVistaHelper($request);
        if (!is_null($request->criterio)) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Tipo: ' . ($request->tipoProforma == 1 ? 'Compra ordinaria' : 'Gran compra') . ', criterio: ' . $request->criterio);
        }
        return response()->json(array('body' => $helper->generarLista(Auth::user()), 'footer' => $helper->generarPaginacionProformas()), 200);
    }

    public function actualizarSeleccion(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso para realizar esta acción', 'tipo' => 'error'), 200);
        }
        $detalle = ProductoDetalle::find($request->id);
        //Busca si el requerimiento está en estado pendiente
        $requerimiento = Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos', 'proforma_paquete_id', 'proformas_paquete.id')
            ->where('proforma_paquete_productos.id', $detalle->proforma_paquete_producto_id)->select(['proformas_paquete.*'])->first();
        if ($requerimiento->estado == 'PENDIENTE') {
            DB::beginTransaction();
            try {
                //Si se va a marcar como seleccionado un producto
                if ($request->seleccionado) {
                    //Busca el producto seleccionado para capturar su costo de envio
                    $seleccionado = ProductoDetalle::where('proforma_paquete_producto_id', $detalle->proforma_paquete_producto_id)->where('seleccionado', true)->first();
                    if ($seleccionado != null) {
                        $costoEnvioSeleccionado = EnvioDetalle::join('mgcp_acuerdo_marco.proforma_paquete_envios', 'proforma_paquete_envio_id', 'proforma_paquete_envios.id')
                            ->join('mgcp_acuerdo_marco.proforma_paquete_destinos', 'proforma_paquete_destino_id', 'proforma_paquete_destinos.id')
                            ->where('proforma_paquete_id', $requerimiento->id)->where('nro_proforma', $seleccionado->nro_proforma)->first();
                        $costoEnvioSeleccionar = EnvioDetalle::join('mgcp_acuerdo_marco.proforma_paquete_envios', 'proforma_paquete_envio_id', 'proforma_paquete_envios.id')
                            ->join('mgcp_acuerdo_marco.proforma_paquete_destinos', 'proforma_paquete_destino_id', 'proforma_paquete_destinos.id')
                            ->where('proforma_paquete_id', $requerimiento->id)->where('nro_proforma', $detalle->nro_proforma)->select(['proforma_paquete_envio_detalles.*'])->first();
                        $costoEnvioSeleccionar->costo_envio_publicar = $costoEnvioSeleccionado->costo_envio_publicar;
                        $costoEnvioSeleccionar->save();
                    }
                }
                //Actualiza el costo del producto
                ProductoDetalle::where('proforma_paquete_producto_id', $detalle->proforma_paquete_producto_id)->update(['seleccionado' => false]);
                $dataAnterior['seleccionado'] = $detalle->seleccionado;
                $dataNueva['seleccionado'] = $request->seleccionado;
                $detalle->seleccionado = $request->seleccionado;
                $detalle->save();
                DB::commit();

                $productoAm = Producto::find($detalle->id_producto);
                LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2,$detalle->getTable(), $dataAnterior, $dataNueva, 'ID: '.$request->id.', tipo: '.($requerimiento->tipo==1 ? 'Compra ordinaria' : 'Gran compra').', requerimiento: ' . $requerimiento->requerimiento . ', empresa: ' . Empresa::find($requerimiento->id_empresa)->empresa . ', producto: ' . $productoAm->marca . ' ' . $productoAm->modelo . ' ' . $productoAm->part_no);
                return response()->json(array('mensaje' => 'Actualizado', 'tipo' => 'success'), 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(array('mensaje' => 'Problema al actualizar, intente de nuevo. Mensaje: ' . $ex->getMessage(), 'tipo' => 'error'), 200);
            }
        } else {
            return response()->json(array('mensaje' => 'El requerimiento debe estar en estado PENDIENTE para cambiar de producto', 'tipo' => 'error'), 200);
        }
    }

    public function actualizarCostoEnvio(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso para realizar esta acción', 'tipo' => 'error'), 200);
        }
        
        $requerimiento = Paquete::join('mgcp_acuerdo_marco.proforma_paquete_destinos', 'proforma_paquete_id', 'proformas_paquete.id')
            ->where('nro_requerimiento_entrega', $request->requerimientoEntrega)->where('id_empresa', $request->idEmpresa)->select(['proformas_paquete.*'])->first();
        if ($requerimiento->estado != 'PENDIENTE') {
            return response()->json(array('mensaje' => 'El requerimiento debe estar en estado PENDIENTE para actualizar el costo de envío', 'tipo' => 'error'), 200);
        }

        $productoSeleccionado = Paquete::generarConsultaEnvioDetalle($request->proforma, $request->idEmpresa, $request->requerimientoEntrega);
        if ($productoSeleccionado == null) {
            return response()->json(array('mensaje' => 'Primero seleccione un producto antes de registrar el costo de envío', 'tipo' => 'warning'), 200);
        } else {
            DB::beginTransaction();
            try {
                $detalle = EnvioDetalle::find($productoSeleccionado->id);
                    $dataAnterior['costo_envio_publicar'] = $detalle->costo_envio_publicar ?? '';
                    $dataNueva['costo_envio_publicar'] = $request->costo;
                    $detalle->costo_envio_publicar = $request->costo;
                $detalle->save();

                    $requerimiento->id_ultimo_usuario = Auth::user()->id;
                    $requerimiento->fecha_cotizacion = new Carbon();
                $requerimiento->save();
                DB::commit();
                LogActividad::registrar(Auth::user(), $this->nombreFormulario,2,$detalle->getTable(),$dataAnterior,$dataNueva,'ID: '.$productoSeleccionado->id.', tipo: '.($requerimiento->tipo==1 ? 'Compra ordinaria' : 'Gran compra').', requerimiento: ' . $requerimiento->requerimiento . ', empresa: ' . Empresa::find($requerimiento->id_empresa)->empresa);

                return response()->json(array('mensaje' => 'Actualizado', 'tipo' => 'success'), 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(array('mensaje' => 'Hubo un problema al actualizar el costo. Por favor intente de nuevo', 'tipo' => 'error'), 200);
            }
        }
    }

    public function actualizarPrecio(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso para realizar esta acción', 'tipo' => 'error'), 200);
        }
        $detalle = ProductoDetalle::find($request->id);
        //Busca si el requerimiento está en estado pendiente
        $requerimiento = Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos', 'proforma_paquete_id', 'proformas_paquete.id')
            ->where('proforma_paquete_productos.id', $detalle->proforma_paquete_producto_id)->select(['estado', 'requerimiento','proformas_paquete.id','id_empresa'])->first();

        if ($requerimiento->estado == 'PENDIENTE') {
            DB::beginTransaction();
            try {
                $dataAnterior['precio_publicar'] = $detalle->precio_publicar ?? '';
                $dataNueva['precio_publicar'] = $request->precio;
                $detalle->precio_publicar = $request->precio;
                $detalle->save();
                    $requerimiento->id_ultimo_usuario = Auth::user()->id;
                    $requerimiento->fecha_cotizacion = new Carbon();
                $requerimiento->save();
                DB::commit();
                
                LogActividad::registrar(Auth::user(), $this->nombreFormulario,2,$detalle->getTable(),$dataAnterior,$dataNueva,'ID: '.$request->id.', tipo: '.($requerimiento->tipo==1 ? 'Compra ordinaria' : 'Gran compra').', requerimiento: ' . $requerimiento->requerimiento.', proforma: '.$detalle->proforma . ', empresa: ' . Empresa::find($requerimiento->id_empresa)->empresa);
                return response()->json(array('mensaje' => 'Actualizado', 'tipo' => 'success'), 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(array('mensaje' => 'Problema al actualizar, intente de nuevo. Mensaje: ' . $ex->getMessage(), 'tipo' => 'error'), 200);
            }
        } else {
            return response()->json(array('mensaje' => 'El requerimiento debe estar en estado PENDIENTE para actualizar el precio', 'tipo' => 'error'), 200);
        }
    }
}
