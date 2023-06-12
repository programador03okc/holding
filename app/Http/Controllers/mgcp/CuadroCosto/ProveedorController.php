<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Proveedor;

class ProveedorController extends Controller {

    public function registrar(Request $request) {
        if (empty($request->ruc))
        {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Ingrese un RUC / DNI'), 200);
        }
        if (empty($request->razonSocial))
        {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Ingrese la razón social'), 200);
        }
        $razon = mb_strtoupper($request->razonSocial);
        $existe= Proveedor::where('razon_social', $razon)->first() ?? Proveedor::where('ruc', $request->ruc)->first();
        if ($existe == null) {
            $proveedor = new Proveedor;
            $proveedor->ruc=$request->ruc;
            $proveedor->razon_social = $razon;
            $proveedor->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'El proveedor ha sido registrado y seleccionado', 'proveedor' => $proveedor), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Ya existe el proveedor ingresado'), 200);
        }
    }

}
