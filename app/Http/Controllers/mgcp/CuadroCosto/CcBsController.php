<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedorView;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CcBs;
use App\Models\mgcp\CuadroCosto\CcBsFila;
use Illuminate\Support\Facades\DB;
use App\Models\mgcp\CuadroCosto\CcBsProveedor;
use App\Models\mgcp\CuadroCosto\HistorialPrecio;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CcBsController extends Controller
{

    private $camposNoEditables = array('id', 'id_cc', 'id_cc_bs');

    public function actualizarCampoFila(Request $request)
    {
        $campo = $request->campo;
        $ccFila = CcBsFila::find($request->id);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($ccFila->id_cc_bs), Auth::user()) != 'ninguno' && !in_array($request->campo, $this->camposNoEditables)) {
            $valor=htmlspecialchars_decode(strip_tags(trim(str_replace(['&nbsp;'], "",$request->valor))));
            //$valor = str_replace("&nbsp;", "", htmlentities(strip_tags(trim($request->valor)), null, 'utf-8'));
            if (in_array($campo, array('cantidad'))) {
                $valor = str_replace(',', '', $valor);
            }

            //Si se va a actualizar la cantidad se debe ver si no va a exceder el consumo de fondo de proveedores
            if ($campo == 'cantidad') {
                $idFondo = $ccFila->bsProveedor == null ? null : $ccFila->bsProveedor->id_fondo_proveedor;
                if ($idFondo != null) {
                    DB::beginTransaction();
                    $cantidadAnterior = $ccFila->cantidad;
                    //Se pone en 0 para liberar el uso del fondo de proveedores
                    $ccFila->cantidad = 0;
                    $ccFila->save();
                    $fondo = FondoProveedorView::find($idFondo);
                    if ($fondo->cantidad_disponible < intval($valor)) {
                        DB::rollBack();
                        return response()->json(array('tipo' => 'danger', 'mensaje' => "No hay suficientes fondos disponibles: La cantidad ingresada es $valor y la cantidad de fondos disponibles es de $fondo->cantidad_disponible. Ingrese una cantidad menor o elimine el fondo de proveedor y vuelva a intentarlo.", 'valor' => $cantidadAnterior), 200);
                    } else {
                        DB::commit();
                    }
                }
            }

            $ccFila->$campo = $valor == '' ? null : $valor;
            $ccFila->save();

            return response()->json(array('tipo' => 'success', 'mensaje' => 'Ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function actualizarCompraFila(Request $request)
    {
        $ccFila = CcBsFila::find($request->idFila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($ccFila->id_cc_bs), Auth::user()) != 'ninguno') {
            $ccFila->comprado = !$ccFila->comprado;
            $ccFila->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha marcado la fila como ' . ($ccFila->comprado ? 'comprada' : 'no comprada')), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function actualizarCampoProveedor(Request $request)
    {
        $campo = $request->campo;
        $ccAm = CcBsProveedor::find($request->idFila);
        $fila = CcBsFila::find($ccAm->id_fila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($fila->id_cc_bs), Auth::user()) != 'ninguno' && !in_array($request->campo, $this->camposNoEditables)) {
            switch ($campo) {
                case 'precio':
                    $ccAm->$campo = str_replace(',', '', $request->valor);
                    $historial = new HistorialPrecio;
                    $historial->tabla = 'ccBs';
                    $historial->id_fila = $request->idFila;
                    $historial->precio = str_replace(',', '', $request->valor);
                    $historial->id_responsable = Auth::user()->id;
                    $historial->save();
                    break;
                case 'plazo':
                    $ccAm->$campo = $request->valor;
                    break;
                case 'flete':
                    $ccAm->$campo = str_replace(',', '', $request->valor);
                    break;
            }
            $ccAm->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function eliminarProveedorFila(Request $request)
    {
        $ccAmProv = CcBsProveedor::find($request->idFilaProveedor);
        $fila = CcBsFila::find($ccAmProv->id_fila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($fila->id_cc_bs), Auth::user()) != 'ninguno') {
            DB::beginTransaction();
            $fila->proveedor_seleccionado = null;
            $fila->save();
            HistorialPrecio::where('tabla', 'ccAm')->where('id_fila', $request->idFilaProveedor)->delete();
            CcBsProveedor::destroy($request->idFilaProveedor);
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila seleccionada'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function obtenerHistorialPrecios(Request $request)
    {
        $historial = HistorialPrecio::with('user')->where('id_fila', $request->idFila)->where('tabla', 'ccBs')->orderBy('fecha', 'desc')->get();
        return response()->json($historial, 200);
    }

    public function agregarFila(Request $request)
    {
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($request->idCuadro), Auth::user()) == 'corporativo') {
            $ccBsFila = new CcBsFila;
            $ccBsFila->id_cc_bs = $request->idCuadro;
            $ccBsFila->creado_por = Auth::user()->id;
            $ccBsFila->unidad = 'UND';
            $ccBsFila->fecha_creacion = new Carbon();
            $ccBsFila->comprado=false;
            $ccBsFila->save();
            return response()->json(array('tipo' => 'success', 'id' => $ccBsFila->id), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function eliminarFila(Request $request)
    {
        $ccBsFila = CcBsFila::find($request->idFila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($ccBsFila->id_cc_bs), Auth::user()) == 'corporativo') {
            DB::beginTransaction();
            $ccBsFila->proveedor_seleccionado = null;
            $ccBsFila->save();
            $filasProveedor = CcBsProveedor::where('id_fila', $request->idFila)->get();
            foreach ($filasProveedor as $fila) {
                HistorialPrecio::where('tabla', 'ccBs')->where('id_fila', $fila->id)->delete();
            }
            CcBsProveedor::where('id_fila', $request->idFila)->delete();
            CcBsFila::destroy($request->idFila);
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function obtenerProveedoresFila(Request $request)
    {
        $proveedores = CcBsProveedor::with(['proveedor', 'fondoProveedor'])->where('id_fila', $request->idFila)->orderBy('id', 'desc')->get();
        $provSeleccionado = CcBsFila::find($request->idFila)->proveedor_seleccionado;
        return response()->json(array('proveedores' => $proveedores, 'provSeleccionado' => $provSeleccionado), 200);
    }

    public function agregarProveedorFila(Request $request)
    {
        $fila = CcBsFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_bs);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {

            if ($request->fondo != 0) {
                $fondo = FondoProveedorView::find($request->fondo);
                if ($fondo->cantidad_disponible < $fila->cantidad) {
                    return response()->json(array('tipo' => 'danger', 'mensaje' => "No hay suficientes fondos disponibles: La cantidad a comprar es de $fila->cantidad y la cantidad de fondos disponibles es de $fondo->cantidad_disponible. Seleccione otro fondo y vuelva a intentarlo."), 200);
                }
            } else {
                $fondo = null;
            }

            DB::beginTransaction();
            $proveedor = new CcBsProveedor;
            $proveedor->id_fila = $request->idFila;
            $proveedor->id_proveedor = $request->proveedor;
            $proveedor->precio = str_replace(',', '', $request->precio);
            $proveedor->moneda = $request->moneda;
            $proveedor->plazo = $request->plazo;
            $proveedor->flete = $request->flete;
            $proveedor->comentario = $request->comentario;
            $proveedor->id_fondo_proveedor=$request->fondo==0 ? null : $request->fondo;
            $proveedor->save();

            $historial = new HistorialPrecio;
            $historial->tabla = 'ccBs';
            $historial->id_fila = $proveedor->id;
            $historial->precio = str_replace(',', '', $request->precio);
            $historial->id_responsable = Auth::user()->id;
            $historial->fecha=new Carbon();
            $historial->save();
            DB::commit();
            return response()->json(array('tipo' => 'success', 'proveedor' => $proveedor, 'fondo' => $fondo), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function seleccionarMejorPrecio(Request $request)
    {
        $proveedores = CcBsProveedor::where('id_fila', $request->idFila)->get();
        $fila = CcBsFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_bs);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {

            //Definir el monto menor inicial
            $menorPrecio = null;
            $id = 0;
            if ($proveedores->count() > 0) {
                foreach ($proveedores as $proveedor) {
                    $precio = ($proveedor->moneda == 'd' ? ($proveedor->precio * $cuadro->tipo_cambio) : $proveedor->precio) + $proveedor->flete;
                    $fondo = $proveedor->fondoProveedor;
                    if ($fondo != null) {
                        //Conversión del fondo de proveedor de acuerdo a la moneda del proveedor
                        if ($proveedor->moneda == $fondo->moneda) {
                            $precio -= $fondo->valor_unitario;
                        } else {
                            $precio -= ($proveedor->moneda == 's' ? ($fondo->valor_unitario * $cuadro->tipo_cambio) : ($fondo->valor_unitario / $cuadro->tipo_cambio));
                        }
                    }
                    if ($menorPrecio == null || $precio < $menorPrecio) {
                        $menorPrecio = $precio;
                        $id = $proveedor->id;
                    }
                }

                DB::beginTransaction();
                $proveedor = CcBsProveedor::find($id);
                if ($proveedor->id_fondo_proveedor != null) {
                    //Se borra el proveedor para que no se considere el fondo de proveedor de la fila
                    $fila->proveedor_seleccionado = null;
                    $fila->save();
                    $fondoView = FondoProveedorView::find($proveedor->id_fondo_proveedor);
                    if ($fondoView->cantidad_disponible < $fila->cantidad) {
                        DB::rollBack();
                        $razonSocial = $proveedor->proveedor->razon_social;
                        return response()->json(array('tipo' => 'danger', 'mensaje' => "El mejor precio (proveedor: $razonSocial) tiene problemas: La cantidad de productos a comprar es de $fila->cantidad y la cantidad de fondos disponibles de $fondoView->descripcion es de $fondoView->cantidad_disponible. Seleccione otro proveedor de forma manual o agregue unidades al fondo y vuelva a intentarlo."), 200);
                    }
                }
                //Actualiza el proveedor seleccionado
                $fila->proveedor_seleccionado = $id;
                $fila->save();
                DB::commit();
                return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'id' => $id), 200);
            } else {
                return response()->json(array('tipo' => 'error', 'mensaje' => 'No hay precios registrados'), 200);
            }
        }
    }

    public function seleccionarProveedorFila(Request $request)
    {
        $fila = CcBsFila::find($request->idFila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($fila->id_cc_bs), Auth::user()) != 'ninguno') {
            DB::beginTransaction();
            $filaProveedor = CcBsProveedor::find($request->idFilaProveedor);
            if ($filaProveedor->id_fondo_proveedor != null) {
                //Se borra el proveedor para que no se considere el fondo de proveedor de la fila
                $fila->proveedor_seleccionado = null;
                $fila->save();
                $fondoProveedor = FondoProveedorView::find($filaProveedor->id_fondo_proveedor);
                if ($fondoProveedor->cantidad_disponible < $fila->cantidad) {
                    DB::rollBack();
                    return response()->json(array('tipo' => 'danger', 'mensaje' => "No hay suficientes fondos disponibles: La cantidad de productos a comprar es de $fila->cantidad y la cantidad de fondos disponibles de $fondoProveedor->descripcion es de $fondoProveedor->cantidad_disponible. Seleccione otro proveedor o agregue unidades al fondo y vuelva a intentarlo."), 200);
                }
            }
            $fila->proveedor_seleccionado = $request->idFilaProveedor;
            $fila->save();
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function buscarNroParte(Request $request)
    {
        $filaActualizar = CcBsFila::find($request->idFila);

        if (CuadroCosto::tipoEdicion(CuadroCosto::find($filaActualizar->id_cc_bs), Auth::user()) == 'corporativo') {
            $filaActualizar->part_no = str_replace("&nbsp;", "", htmlentities(strip_tags(trim(mb_strtoupper($request->criterio))), ENT_COMPAT, 'utf-8'));
            if ($filaActualizar->part_no != '') {

                $filaCc = CcBsFila::where('part_no', $filaActualizar->part_no)->orderBy('id', 'desc')->first();
                if ($filaCc != null) {
                    $filaActualizar->descripcion = $filaCc->descripcion;
                }
            }
            $filaActualizar->save();
            return response()->json(array('tipo' => 'success', 'descripcion' => $filaActualizar->descripcion), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro de costo está en modo de sólo lectura'), 200);
        }
    }
}
