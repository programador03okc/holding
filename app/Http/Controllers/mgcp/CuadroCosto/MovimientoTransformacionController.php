<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcFilaMovimientoTransformacion;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoTransformacionController extends Controller
{
    private $nombreFormulario = 'Transformación de producto';

    public function agregarFila(Request $request)
    {
        $filaCuadro = CcAmFila::find($request->id);
        $cuadro = CuadroCosto::find($filaCuadro->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            $fila = new CcFilaMovimientoTransformacion();
                $fila->id_fila_base = $request->id;
            $fila->save();

            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $fila->getTable(), null, $fila, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad);
            $filasCuadro = CcAmFila::where('id_cc_am', $filaCuadro->id_cc_am)->orderBy('id', 'asc')->get();

            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha agregado una fila al listado', 'id' => $fila->id, 'filasCuadro' => $filasCuadro), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro de costo está en modo de sólo lectura'), 200);
        }
    }

    public function eliminarFila(Request $request)
    {
        $filaTransformacion = CcFilaMovimientoTransformacion::find($request->id);
        $filaCuadro = CcAmFila::find($filaTransformacion->id_fila_base);
        $cuadro = CuadroCosto::find($filaCuadro->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $filaTransformacion->getTable(), $filaTransformacion, null, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad);
            $filaTransformacion->delete();

            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro de costo está en modo de sólo lectura'), 200);
        }
    }
    public function actualizarFila(Request $request)
    {
        $campo = $request->campo;
        $filaTransformacion = CcFilaMovimientoTransformacion::find($request->id);
        $filaCuadro = CcAmFila::find($filaTransformacion->id_fila_base);
        $cuadro = CuadroCosto::find($filaCuadro->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            $valor = htmlspecialchars_decode(strip_tags(trim(str_replace(['&nbsp;'], "", $request->valor))));
                $dataAnterior[$campo] = $filaTransformacion->$campo ?? '';
                $filaTransformacion->$campo = ($valor == '' || $valor==0) ? null : $valor;
                $dataNueva[$campo] = $filaTransformacion->$campo ?? '';
            $filaTransformacion->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $filaTransformacion->getTable(), $dataAnterior, $dataNueva, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad . 'ID fila: ' . $request->id);
            
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha actualizado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro de costo está en modo de sólo lectura'), 200);
        }
    }

    public function obtenerDetalles(Request $request)
    {
        $filaCuadro = CcAmFila::find($request->idFila);
        $cuadro = CuadroCosto::find($filaCuadro->id_cc_am);
        $movimientos = CcFilaMovimientoTransformacion::where('id_fila_base', $request->idFila)->orderBy('id', 'asc')->get();
        $filasCuadro = CcAmFila::where('id_cc_am', $filaCuadro->id_cc_am)->orderBy('id', 'asc')->get();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'CDP: ' . $cuadro->oportunidad->codigo_oportunidad . 'ID fila: ' . $request->idFila);
        
        return response()->json(array('tipo' => 'success', 'filaCuadro' => $filaCuadro, 'movimientos' => $movimientos, 'filasCuadro' => $filasCuadro), 200);
        
    }
}
