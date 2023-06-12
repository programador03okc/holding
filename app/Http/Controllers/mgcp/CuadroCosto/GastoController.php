<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Gasto;
use App\Models\mgcp\CuadroCosto\CcGasto;
use App\Models\mgcp\CuadroCosto\TipoOperacion;
use App\Models\mgcp\CuadroCosto\TipoAfectacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastoController extends Controller {

    public function index() {
        $gastos = Gasto::listar();
        return view('cuadrocostos.gasto.index')->with(compact('gastos'));
    }

    public function registrar(Request $request) {
        try {
            if ($request->desde>$request->hasta)
            {
                $request->session()->flash('alert-danger', 'El monto desde no puede ser mayor al monto de hasta.');
                return \Redirect::route('cuadro-costo.gasto.nuevo')->withInput();
            }
            $existe = null; //Gasto::where('concepto', $request->concepto)->where('id_operacion', $request->operacion)->first();
            if ($existe == null) {
                $gasto = new Gasto;
                $gasto->concepto = $request->concepto;
                $gasto->id_operacion = $request->operacion;
                $gasto->id_afectacion = $request->afectacion;
                $gasto->porcentaje = $request->porcentaje;
                $gasto->desde = $request->desde;
                $gasto->hasta = $request->hasta;
                $gasto->save();
                $request->session()->flash('alert-success', 'Datos registrados.');
                return \Redirect::route('cuadro-costo.gasto.nuevo');
            } else {
                $operacion = TipoOperacion::find($request->operacion);
                $request->session()->flash('alert-danger', 'El concepto ' . $request->concepto . ' ya existe para la operación ' . $operacion->tipo_operacion . '. Seleccione otra operación o ingrese otro concepto e intente de nuevo.');
                return \Redirect::route('cuadro-costo.gasto.nuevo')->withInput();
            }
        } catch (\PDOException $ex) {
            $request->session()->flash('alert-danger', 'Hubo un problema al registrar los datos. Por favor intente de nuevo.');
            return \Redirect::route('cuadro-costo.gasto.nuevo')->withInput();
        }
    }

    public function nuevo() {
        $operacion = 'nuevo';
        $tiposAfectacion = TipoAfectacion::orderBy('tipo_afectacion')->get();
        $tiposOperacion = TipoOperacion::orderBy('tipo_operacion')->get();
        return view('cuadrocostos.gasto.form')->with(compact('operacion', 'tiposOperacion', 'tiposAfectacion'));
    }

    public function eliminar(Request $request) {
        $ccGastos = CcGasto::where('id_gasto', $request->id)->get();
        foreach ($ccGastos as $gasto) {
            $gasto->delete();
        }
        $gasto = Gasto::find($request->id);
        $gasto->delete();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Registro eliminado.'));
    }

    public function activar(Request $request) {
        if ($request->activar == 'true') {
            $gasto = new CcGasto;
            $gasto->id_cc = $request->idCuadro;
            $gasto->id_gasto = $request->id;
            $gasto->save();
        } else {
            $gasto = CcGasto::where('id_gasto', $request->id)->where('id_cc', $request->idCuadro)->first();
            if ($gasto != null) {
                $gasto->delete();
            }
        }
        return response()->json(array('tipo' => 'success'));
    }

}
