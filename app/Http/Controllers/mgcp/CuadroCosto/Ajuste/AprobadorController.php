<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Ajuste;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\AprobadorUno;
use App\Models\mgcp\CuadroCosto\AprobadorTres;
use Illuminate\Support\Facades\Auth;

class AprobadorController extends Controller {

    public function index() {
        if (!Auth::user()->tieneRol(58)) {
             return view('mgcp.usuario.sin_permiso');
        }
        $aprobadorFueraMonto = AprobadorTres::where('id_empresa', Auth::user()->id_empresa)->first();
        $usuarios = User::where('activo', true)->where('id_empresa', Auth::user()->id_empresa)->orderBy('name', 'asc')->get();
        return view('mgcp.cuadro-costo.ajuste.aprobadores')->with(compact('aprobadorFueraMonto', 'usuarios'));
    }

    public function dataLista(Request $request) {
        $data = AprobadorUno::join('mgcp_usuarios.users', 'id_usuario', '=', 'users.id')
                ->select(['aprobadores_tipo_uno.id', 'users.name', 'valor_venta', 'margen_minimo'])->where('aprobadores_tipo_uno.id_empresa', Auth::user()->id_empresa);
        return datatables($data)->toJson();
    }

    public function actualizarAprobadorFueraMonto(Request $request) {
        $aprobador = AprobadorTres::where('id_empresa', Auth::user()->id_empresa)->find($request->id);
        if ($aprobador == null) {
            return response()->json(array('mensaje' => 'No hay aprobador registrado. Contacte con el administrador del sistema', 'tipo' => 'danger'), 200);
        }

        $usuario = User::where('id', $request->usuario)->where('activo', true)->first();
        if ($usuario == null) {
            return response()->json(array('mensaje' => 'No existe el usuario ingresado', 'tipo' => 'danger'), 200);
        }
        
            $aprobador->id_usuario = $request->usuario;
            $aprobador->id_empresa = Auth::user()->id_empresa;
        $aprobador->save();
        return response()->json(array('mensaje' => 'El aprobador ha sido actualizado', 'tipo' => 'success'), 200);
    }

    public function eliminarAprobadorPorMonto(Request $request) {
        $aprobador = AprobadorUno::find($request->id);
        $aprobador->delete();
        return response()->json(array('mensaje' => 'Ok', 'tipo' => 'success'), 200);
    }

    public function registrarAprobadorPorMonto(Request $request) {
        $validator = Validator::make($request->all(), [
            'usuario' => 'required',
            'valor_venta' => 'required|numeric',
            'margen_minimo' => 'required|numeric|gt:0|lt:101',
        ]);

        if ($validator->fails()) {
            return response()->json(array('mensaje' => 'Error en los campos','tipo'=>'danger'),200);
        }

        $existe = AprobadorUno::where('id_usuario', $request->usuario)->where('id_empresa', Auth::user()->id_empresa)->first();
        if ($existe != null) {
            return response()->json(array('mensaje' => 'El aprobador ya existe. Seleccione otro aprobador e intente de nuevo', 'tipo' => 'danger'), 200);
        }
        $aprobador = new AprobadorUno;
            $aprobador->id_usuario = $request->usuario;
            $aprobador->valor_venta = $request->valor_venta;
            $aprobador->margen_minimo = $request->margen_minimo;
            $aprobador->id_empresa = Auth::user()->id_empresa;
        $aprobador->save();
        return response()->json(array('mensaje' => 'Se ha registrado al aprobador', 'tipo' => 'success'),200);
    }
    
    public function actualizarAprobadorPorMonto(Request $request) {
        $validator = Validator::make($request->all(), [
            'usuario' => 'required',
            'valor_venta' => 'required|numeric',
            'margen_minimo' => 'required|numeric|gt:0|lt:101',
        ]);

        if ($validator->fails()) {
            return response()->json(array('mensaje' => 'Error en los campos','tipo'=>'danger'),200);
        }

        $existe = AprobadorUno::where('id', '!=', $request->id)->where('id_usuario', $request->usuario)->where('id_empresa', Auth::user()->id_empresa)->first();
        if ($existe != null) {
            return response()->json(array('mensaje' => 'El aprobador ya existe. Seleccione otro aprobador e intente de nuevo', 'tipo' => 'danger'), 200);
        }
        $aprobador = AprobadorUno::find($request->id);
            $aprobador->id_usuario = $request->usuario;
            $aprobador->valor_venta = $request->valor_venta;
            $aprobador->margen_minimo = $request->margen_minimo;
            $aprobador->id_empresa = Auth::user()->id_empresa;
        $aprobador->save();
        return response()->json(array('mensaje' => 'El aprobador ha sido actualizado', 'tipo' => 'success'),200);
    }
    
    public function detallesAprobadorPorMonto(Request $request)
    {
        return response()->json(AprobadorUno::find($request->id),200);
    }
}
