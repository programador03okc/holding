<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualRegistroHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPortalHelper;
use App\Helpers\mgcp\AcuerdoMarco\StockHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\StockEmpresaPublicar;
use App\Models\mgcp\AcuerdoMarco\Proforma\ComentarioCompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\ComentarioGranCompra;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;

class ProformaIndividualController extends Controller
{
    private $nombreFormulario = 'Proforma individual de Acuerdo marco';

    public function obtenerListaParaEnviarPortal(Request $request)
    {
        if ($request->tipoProforma == 1) {
            $proformas = CompraOrdinaria::with('empresa', 'producto', 'usuario')->where('estado', 'PENDIENTE')->where('id_empresa', Auth::user()->id_empresa)
            ->whereRaw('(costo_envio_publicar >= 0 OR restringir = true)')->orderBy('id_ultimo_usuario', 'asc')->orderBy('id_empresa', 'asc')->get();
        } else {
            $proformas = GranCompra::with('empresa', 'producto', 'usuario')->where('estado', 'PENDIENTE')->where('id_empresa', Auth::user()->id_empresa)
                ->where('costo_envio_publicar', '>=', 0)->orderBy('id_ultimo_usuario', 'asc')->orderBy('id_empresa', 'asc')->get();
        }
                
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 15, null, null,null,($request->tipoProforma == 1 ? 'Compra ordinaria' : 'Gran compra'),);
        return response()->json($proformas, 200);
    }

    private function publicarStock(Empresa $empresa, Producto $producto)
    {
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        $campoPrecio = 'precio_' . $empresa->nombre_corto;
        $stockEspecifico = StockEmpresaPublicar::where('id_producto', $producto->id)->where('id_empresa', $empresa->id)->first();
        $stockPublicar = is_null($stockEspecifico) ? StockHelper::calcularCantidad($producto->$campoPrecio, $producto->moneda, $tipoCambio) : $stockEspecifico->stock;
        StockHelper::publicar($empresa, $producto, $stockPublicar, $stockPublicar, true);
    }

    public function enviarCotizacionPortal(Request $request)
    {
        $proforma = $request->tipoProforma == 1 ? CompraOrdinaria::find($request->idProforma) : GranCompra::find($request->idProforma); //es nroProforma pero para evitar problemas con archivos viejos de JS se conserva
        if ($proforma->estado != 'PENDIENTE') {
            return response()->json(array('mensaje' => 'Ya enviada', 'tipo' => 'success'), 200);
        }
        $empresa = Empresa::find($proforma->id_empresa);
        $portalHelper = new PeruComprasHelper();

        if (!$portalHelper->login($empresa, 3)) { // ealvarez 2
            return response()->json(array('mensaje' => 'Error al iniciar sesión', 'tipo' => 'danger'), 200);
        } else {
            $resultado = $proforma->restringir ? ProformaPortalHelper::restringir($portalHelper, $proforma) : ProformaPortalHelper::proformaIndividualEnviarCotizacion($portalHelper, $proforma);
            $this->publicarStock($empresa, Producto::find($proforma->id_producto));

            if ($resultado->mensaje_rpta == 'Ejecutado Correctamente') {
                $proforma->estado = ($proforma->restringir ? 'RESTRINGIDA' : 'COTIZADA');
                $proforma->save();
                return response()->json(array('mensaje' => 'Enviada', 'tipo' => 'success'), 200);
            } else {
                if ($resultado->mensaje_rpta == 'La proforma no existe') {
                    $proforma->estado = 'ANULADA';
                    $proforma->save();
                }
                return response()->json(array('mensaje' => 'Error de Perú Compras: ' . $resultado->mensaje_rpta, 'tipo' => 'danger'), 200);
            }
        }
    }

    public function actualizarRestringir(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }

        $proforma = $request->tipoProforma == 1 ? CompraOrdinaria::find($request->id) : GranCompra::find($request->id);
        if ($proforma->puede_restringir) {
            $dataAnterior['restringida'] = $proforma->restringir ?? false;
            $proforma->restringir = $request->valor == 1;
            $proforma->id_ultimo_usuario = Auth::user()->id;
            $proforma->fecha_cotizacion = new Carbon();
            $proforma->save();
            $dataNueva['restringida'] = $proforma->restringir;

            if ($proforma->restringir) {
                $mensaje = 'Se ha marcado la proforma como restringida para ' . $proforma->empresa->empresa;
            } else {
                $mensaje = 'Se ha retirado la marca de restricción de la proforma para ' . $proforma->empresa->empresa;
            }
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $proforma->getTable(), $dataAnterior, $dataNueva, 'ID proforma: '.$request->id.', proforma: '.$proforma->proforma.', empresa: '. Empresa::find($proforma->id_empresa)->empresa);

            return response()->json(array('tipo' => 'success', 'mensaje' => $mensaje), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Por el monto, no se puede restringir esta proforma'), 200);
        }
    }

    public function actualizarCampo(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }

        $campo = $request->campo;
        $data = $request->tipoProforma == 1 ? CompraOrdinaria::find($request->id) : GranCompra::find($request->id);
        $dataAnterior[$campo] = $data->$campo ?? '';
        $data->$campo = $request->valor;
        $dataNueva[$campo] = $data->$campo;
        $data->id_ultimo_usuario = Auth::user()->id;

        if ($request->tipoProforma == 1) {
            $data->tipo_carga = 'MANUAL';
        }
        $data->fecha_cotizacion = new Carbon();
        $data->save();

        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $data->getTable(), $dataAnterior, $dataNueva, 'ID proforma: '.$request->id.', proforma: '.$data->proforma.', empresa: '. Empresa::find($data->id_empresa)->empresa);

        return response()->json(array('tipo' => 'success'), 200);
    }

    public function deshacerCotizacion(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }
        $proforma = $request->tipoProforma == 1 ? CompraOrdinaria::find($request->id) : GranCompra::find($request->id);
        if ($proforma->puede_deshacer_cotizacion) {
            $dataAnterior['estado'] = $proforma->estado;
            $proforma->estado = 'PENDIENTE';
            $dataNueva['estado'] = $proforma->estado;
            $proforma->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $proforma->getTable(), $dataAnterior, $dataNueva, 'ID proforma: '.$request->id.', proforma: '.$proforma->proforma.', empresa: '. Empresa::find($proforma->id_empresa)->empresa);

            return response()->json(array('tipo' => 'success', 'plazo' => $proforma->plazo_publicar, 'precio' => $proforma->precio_publicar, 'flete' => $proforma->costo_envio_publicar, 'requiereFlete' => ($proforma->requiere_flete ? 1 : 0), 'mensaje' => 'Se ha deshecho la cotización de la proforma ' . $proforma->proforma));
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No se puede deshacer la cotización de la proforma ' . $proforma->proforma . '. Verifique que su estado es COTIZADA y que la fecha límite de cotización sea menor o igual a la fecha de hoy.'));
        }
    }

    public function obtenerDetalles(Request $request)
    {
        $proforma = $request->tipoProforma == 1 ? CompraOrdinaria::with(['entidad', 'empresa', 'producto'])->find($request->id) : GranCompra::with(['entidad', 'empresa', 'producto'])->find($request->id);
        return response()->json($proforma);
    }
    
    /*public function paquete()
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $categorias = Categoria::join('mgcp_acuerdo_marco.catalogos', 'id_catalogo', 'catalogos.id')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')->where('activo', true)
            ->orderBy('categorias.id', 'asc')->select(['categorias.id', 'categorias.descripcion AS categoria', 'acuerdo_marco.descripcion AS acuerdo_marco'])->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_compra_ordinaria ORDER BY estado");
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.productos_am WHERE id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria) ORDER BY marca");
        $fechaActual = new Carbon();
        return view('mgcp.acuerdo-marco.proforma.compra-ordinaria.paquete')->with(compact('empresas', 'estados', 'categorias', 'departamentos', 'marcas', 'fechaActual'));
    }*/



    public function obtenerComentarios(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso', 'tipo' => 'danger'), 200);
        }
        if ($request->tipoProforma == 1) {
            $comentarios = ComentarioCompraOrdinaria::with('usuario')->where('id_proforma', $request->idProforma)->orderBy('fecha', 'asc')->get();
        } else {
            $comentarios = ComentarioGranCompra::with('usuario')->where('id_proforma', $request->idProforma)->orderBy('fecha', 'asc')->get();
        }
        return response()->json(array('comentarios' => $comentarios, 'tipo' => 'success'), 200);
    }

    public function registrarComentario(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('mensaje' => 'Sin permiso', 'tipo' => 'danger'), 200);
        }
        if ($request->tipoProforma == 1) {
            $comentario = new ComentarioCompraOrdinaria;
            $tipo = "Compra ordinaria";
        } else {
            $comentario = new ComentarioGranCompra;
            $tipo = "Gran compra";
        }
            $comentario->id_proforma = $request->idProforma;
            $comentario->id_usuario = Auth::user()->id;
            $comentario->fecha = new Carbon();
            $comentario->comentario = $request->comentario;
        $comentario->save();
        LogActividad::registrar(Auth::user(),  'Se ha registrado un comentario en la proforma de '.$tipo, 2);
        return response()->json(array('tipo' => 'success', 'usuario' => Auth::user()->name, 'fecha' => $comentario->fecha), 200);
    }
}
