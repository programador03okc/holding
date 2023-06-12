<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use Illuminate\Support\Facades\DB;
use App\Models\mgcp\CuadroCosto\CcGg;
use App\Models\mgcp\CuadroCosto\CcGgFila;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CcGgController extends Controller
{

    private $camposNoEditables = array('id', 'id_cc', 'id_cc_gg');

    public function actualizarCampoFila(Request $request)
    {
        $campo = $request->campo;
        $ccFila = CcGgFila::find($request->id);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($ccFila->id_cc_gg), Auth::user()) == 'corporativo' && !in_array($request->campo, $this->camposNoEditables)) {
            //$valor = strip_tags($request->valor);
            $valor=htmlspecialchars_decode(strip_tags(trim(str_replace(['&nbsp;'], "",$request->valor))));
            if (in_array($campo, array('personas', 'porcentaje_participacion', 'tiempo', 'costo'))) {
                $valor = str_replace(',', '', $valor);
            }
            $ccFila->$campo = $valor;
            $ccFila->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function agregarFila(Request $request)
    {
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($request->idCuadro), Auth::user()) == 'corporativo') {
            $ccBsFila = new CcGgFila;
            $ccBsFila->id_cc_gg = $request->idCuadro;
            $ccBsFila->creado_por = Auth::user()->id;
            $ccBsFila->id_categoria_gasto = 1;
            $ccBsFila->unidad = 'UND';
            $ccBsFila->fecha_creacion = new Carbon();
            $ccBsFila->save();
            return response()->json(array('tipo' => 'success', 'id' => $ccBsFila->id), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function eliminarFila(Request $request)
    {
        $fila = CcGgFila::find($request->idFila);
        if (CuadroCosto::tipoEdicion(CuadroCosto::find($fila->id_cc_gg), Auth::user()) == 'corporativo') {
            CcGgFila::destroy($request->idFila);
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }
}
