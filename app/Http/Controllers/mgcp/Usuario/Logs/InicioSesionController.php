<?php

namespace App\Http\Controllers\mgcp\Usuario\Logs;

use App\Models\mgcp\Usuario\Notificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Usuario\LogLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

class InicioSesionController extends Controller {

    public function index(Request $request) {
        if (!Auth::user()->tieneRol(151)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $usuarios = User::withTrashed()->orderBy('name')->get();
        return view('mgcp.usuario.logs.inicio-sesion')->with(compact('usuarios'));
    }

    public function dataLista(Request $request) {
        if (!Auth::user()->tieneRol(151)) {
            return datatables(null)->toJson();
        }
        $this->actualizarFiltros($request);
        $data = LogLogin::join('mgcp_usuarios.users','users.id','id_usuario');
        if ($request->session()->has('logloginUsuario')) {
            $data = $data->where("id_usuario", session('logloginUsuario'));
        }
        if ($request->session()->has('logloginDesde')) {
            $data = $data->whereBetween('fecha', [
                Carbon::createFromFormat('d-m-Y', session('logloginDesde'))->setHour(0)->setMinute(0)->setSecond(0), 
                Carbon::createFromFormat('d-m-Y', session('logloginHasta'))->setHour(23)->setMinute(59)->setSecond(59)
            ]);
        }
        return datatables($data->select(['*']))->toJson();
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkUsuario == 'on') {
            session(['logloginUsuario' => $request->selectUsuario]);
        } else {
            $request->session()->forget('logloginUsuario');
        }
        if ($request->chkFecha == 'on') {
            session(['logloginDesde' => $request->fechaDesde]);
            session(['logloginHasta' => $request->fechaHasta]);
        } else {
            $request->session()->forget('logloginDesde');
            $request->session()->forget('logloginHasta');
        }
    }
}