<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProducto;
use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProductoDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculadoraProductoController extends Controller
{
    public function listar(Request $request)
    {
        $calculadora = CalculadoraProducto::find($request->idProducto);
        if ($calculadora == null) {
            $calculadora = new CalculadoraProducto();
                $calculadora->id_producto = $request->idProducto;
                $calculadora->flete = 0;
                $calculadora->fecha_actualizacion = new Carbon();
                $calculadora->nro_proforma = $request->nroProforma;
                $calculadora->tipo_proforma = $request->tipoProforma;
            $calculadora->save();
        }
        $proformas = $request->tipoProforma == 1 ? CompraOrdinaria::with('empresa') : GranCompra::with('empresa');

        $proformas = $proformas->where('requerimiento', 'like', $request->requerimiento . '%')->where('proforma', $request->proforma)->where('id_producto', $request->idProducto)->orderBy('id_empresa');
        if ($request->empresas!=null) {
            $proformas=$proformas->whereIn('id_empresa',$request->empresas);
        }
        if ($request->estado!=null) {
            $proformas=$proformas->where('estado',$request->estado);
        }
        return response()->json(array('tipo' => 'success', 'calculadora' => $calculadora, 'detalles' => $calculadora->detalles, 'producto' => $calculadora->producto, 'proformas' => $proformas->get()), 200);
    }

    /**
     * Elimina los precios ingresados para todos los productos. No elimina las filas.
     * Esta función se utiliza de forma externa por GET y automatizada por un administrador de tareas
     */
    public function eliminarPrecios()
    {
        $fecha = new Carbon();
        DB::statement("UPDATE mgcp_acuerdo_marco.calculadora_producto_detalles SET monto = 0 WHERE id_producto IN 
        (SELECT calculadora_productos.id_producto FROM mgcp_acuerdo_marco.calculadora_productos WHERE CAST(fecha_actualizacion AS date) < ?)",[$fecha->format('Y-m-d')]);
    }

    public function actualizarCampo(Request $request)
    {
        if ($request->campo == 'flete') {
            $cabecera = CalculadoraProducto::find($request->id);
                $cabecera->flete = $request->valor;
                $cabecera->fecha_actualizacion = new Carbon();
                $cabecera->nro_proforma = $request->proforma;
                $cabecera->tipo_proforma = $request->tipoProforma;
            $cabecera->save();
        } else {
            $campo = $request->campo;
            $detalle = CalculadoraProductoDetalle::find($request->id);
                $detalle->$campo = $request->valor;
                $detalle->nro_proforma = $request->proforma;
                $detalle->tipo_proforma = $request->tipoProforma;
            $detalle->save();

            $cabecera = CalculadoraProducto::find($detalle->id_producto);
                $cabecera->fecha_actualizacion = new Carbon();
                $cabecera->nro_proforma = $request->proforma;
                $cabecera->tipo_proforma = $request->tipoProforma;
            $cabecera->save();
        }
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Campo actualizado'));
    }

    public function agregarFila(Request $request)
    {
        $fila = new CalculadoraProductoDetalle();
            $fila->id_producto = $request->idProducto;
            $fila->nro_proforma = $request->nroProforma;
            $fila->tipo_proforma = $request->tipoProforma;
        $fila->save();
        return response()->json(array('tipo' => 'success', 'id' => $fila->id, 'proforma' => $fila->nro_proforma));
    }

    public function aplicarPreciosProformas(Request $request)
    {
        if (!empty($request->tipo)) {
            $total = count($request->tipo);
            for ($i = 0; $i < $total; $i++) {
                $proforma = $request->tipo[$i] == 1 ? CompraOrdinaria::find($request->codigo[$i]) : GranCompra::find($request->codigo[$i]);
                if ($proforma->estado == 'PENDIENTE' && $proforma->restringir!=true && $request->seleccionado[$i]==1) {
                    $proforma->precio_publicar = $request->precio[$i];
                    if ($proforma->requiere_flete) {
                        $proforma->costo_envio_publicar = $request->flete[$i];
                    }
                    $proforma->id_ultimo_usuario = Auth::user()->id;
                    $proforma->tipo_carga = 'MANUAL';
                    $proforma->fecha_cotizacion=new Carbon();
                    $proforma->save();
                }
            }
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se han aplicado los precios'));
        } else {
            return response()->json(array('tipo' => 'info', 'mensaje' => 'No hay proformas con estado PENDIENTE'));
        }
    }

    public function eliminarFila(Request $request)
    {
        CalculadoraProductoDetalle::destroy($request->id);
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'));
    }
}
