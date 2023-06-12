<?php

namespace App\Http\Controllers\mgcp\Usuario\Logs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActividadUsuarioController extends Controller {

    public function index(Request $request) {
        if (!Auth::user()->tieneRol(150)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $usuarios = User::withTrashed()->orderBy('name')->get();
        return view('mgcp.usuario.logs.actividad-usuario')->with(compact('usuarios'));
    }

    public function dataLista(Request $request) {
        if (!Auth::user()->tieneRol(150)) {
            return datatables(null)->toJson();
        }
        $this->actualizarFiltros($request);
        $data = LogActividad::join('mgcp_usuarios.log_actividad_acciones','log_actividad_acciones.id','id_accion')->join('mgcp_usuarios.users','users.id','id_usuario');

        if ($request->session()->has('logActividadUsuario')) {
            $data = $data->where("id_usuario", session('logActividadUsuario'));
        }
        if ($request->session()->has('logActividadDesde')) {
            $data = $data->whereBetween('fecha', [
                Carbon::createFromFormat('d-m-Y', session('logActividadDesde'))->setHour(0)->setMinute(0)->setSecond(0), 
                Carbon::createFromFormat('d-m-Y', session('logActividadHasta'))->setHour(23)->setMinute(59)->setSecond(59)
            ]);
        }
        return datatables($data->select(['*']))->toJson();
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkUsuario == 'on') {
            session(['logActividadUsuario' => $request->selectUsuario]);
        } else {
            $request->session()->forget('logActividadUsuario');
        }
        if ($request->chkFecha == 'on') {
            session(['logActividadDesde' => $request->fechaDesde]);
            session(['logActividadHasta' => $request->fechaHasta]);
        } else {
            $request->session()->forget('logActividadDesde');
            $request->session()->forget('logActividadHasta');
        }
    }
}