<?php

namespace App\Http\Controllers\mgcp;

use App\Models\User;
use App\Models\mgcp\Usuario\TipoRol;
use App\Models\mgcp\Usuario\RolUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Usuario\HistorialRenovacion;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\mgcp\Usuario\LogLogin;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Browser;

class UsuarioController extends Controller
{

    public function __construct()
    {
    }

    public function lista()
    {
        if (!Auth::user()->tieneRol(17)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Lista de usuarios', 1);
        $users = User::where('id_empresa', Auth::user()->id_empresa)->withTrashed()->orderBy('name', 'asc')->get();
        return View::make('mgcp/usuario/lista')->with('users', $users);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('home');
        return redirect("https://login.microsoftonline.com/common/oauth2/v2.0/logout");
    }

    public function validarLogin(Request $request)
    {
        $login = Socialite::driver('microsoft')->user();
        $usuario = User::where('email', $login->email)->where('activo', true)->whereNull('deleted_at')->first();
        if ($usuario == null) {
            return view('mgcp.usuario.login_no_existe');
        } else {
            Auth::login($usuario);
            LogLogin::registrar($usuario, $request);
            return redirect()->route('home');
        }
    }

    public function nuevo()
    {
        if (!Auth::user()->tieneRol(17)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Nuevo usuario', 1);
        $tiposRol = TipoRol::orderBy('id', 'asc')->get();
        return View::make('mgcp.usuario.formulario')->with('operacion', 'nuevo')->with(compact('tiposRol'));
    }

    public function registrar(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'name'          => 'required|max:255',
            'email'         => 'required|email|max:255',
            'nombre_corto'  => 'required|max:15',
            'password'      => [
                'required', 'confirmed', 'min:6',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/'  // must contain a special character
            ] 
        ]);

        if ($validar->fails()) {
            return Redirect::route('mgcp.usuarios.nuevo')->withErrors($validar)->withInput();
        } else {

            if (User::where('email', $request->email)->count() > 0) {
                $request->session()->flash('alert-danger', 'Ya existe un usuario con el correo ' . $request->email . '. Ingrese otro correo e intente de nuevo');
                return Redirect::route('mgcp.usuarios.nuevo')->withInput();
            }
            try {
                DB::beginTransaction();

                $hoy = Carbon::now();
                $renovacion = $hoy->addDay(15);

                $user = new User;
                    $user->name = trim($request->name);
                    $user->nombre_corto = trim($request->nombre_corto);
                    $user->email = trim($request->email);
                    $user->password = bcrypt(trim($request->password));
                    // $user->password = 'x';//Campo es not null //bcrypt(trim($request->password));
                    $user->activo = true;
                    $user->renovacion = $renovacion;
                    $user->id_empresa = Auth::user()->id_empresa;
                $user->save();
                if (isset($request->rol)) {
                    foreach ($request->rol as $rol) {
                        $usuarioRol = new RolUsuario();
                            $usuarioRol->id_usuario = $user->id;
                            $usuarioRol->id_rol = $rol;
                        $usuarioRol->save();
                    }
                }
                DB::commit();
                LogActividad::registrar(Auth::user(), 'Nuevo usuario', 4, $user->getTable(), null, $user);
                $request->session()->flash('alert-success', 'Se ha registrado al usuario ' . $user->name);
            } catch (Exception $ex) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'Hubo un error al crear al usuario. Por favor inténtelo de nuevo');
            } finally {
                return Redirect::route('mgcp.usuarios.nuevo');
            }
        }
    }

    public function actualizar(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id_usuario'    => 'required',
            'name'          => 'required|max:255',
            'nombre_corto'  => 'required|max:15',
            'email'         => 'required|email|max:255'
        ]);

        if ($validar->fails()) {
            return Redirect::route('mgcp.usuarios.editar', $request->id_usuario)->withErrors($validar)
                ->withInput();
        } else {
            DB::beginTransaction();

            $hoy = Carbon::now();
            $renovacion = $hoy->addDay(15);

            $user = User::withTrashed()->find(trim($request->id_usuario));
                $user->name = trim($request->name);
                $user->nombre_corto = trim($request->nombre_corto);
                $user->email = trim($request->email);
                $user->activo = $request->activo == "1" ? true : false;
                $user->renovacion = $renovacion;
                $user->id_empresa = Auth::user()->id_empresa;
            if ($request->password != '') {
                $user->password = bcrypt(trim($request->password));
            }
            if ($request->activo == "0") {
                $user->deleted_at = new Carbon();
            } else if ($request->activo == "1") {
                $user->deleted_at = null;
            }
            $user->save();
            LogActividad::registrar(Auth::user(), 'Actualizar usuario', 2, $user->getTable(), $user->getOriginal(), $user);
			
            RolUsuario::where('id_usuario', $user->id)->delete();
            if (isset($request->rol)) {
                foreach ($request->rol as $rol) {
                    $usuarioRol = new RolUsuario;
                    $usuarioRol->id_usuario = $user->id;
                    $usuarioRol->id_rol = $rol;
                    $usuarioRol->save();
                }
            }
            DB::commit();
            $request->session()->flash('alert-success', 'El usuario ' . $user->name . ' se ha actualizado');
            return Redirect::route('mgcp.usuarios.editar', trim($request->id_usuario));
        }
    }

    public function editar($id)
    {
        /* if (Auth::user()->roles()->where('id_rol', 17)->count() == 0) {
          return redirect()->route('home');
          } */
        $user = User::withTrashed()->find($id);
        $tiposRol = TipoRol::orderBy('id', 'asc')->get();
        return View::make('mgcp.usuario.formulario')
            ->with('operacion', 'editar')
            ->with(compact('tiposRol', 'user'));
    }

    public function renovarClave(Request $request)
    {
        $date = new DateTime();
        $new_date = $date->modify('+10 day');
        try {
            $validar = Validator::make($request->all(), [
                'password'      => [
                    'required', 'min:6',
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/'  // must contain a special character
                ] 
            ]);

            if ($validar->fails()) {
                $response = 'validate';
                $alert = 'info';
                $msj = 'Clave insegura, intente ingresar una nueva';
                $error = $validar->errors();
            } else {
                $data = User::findOrFail(Auth::user()->id);
                    $data->password = bcrypt(trim($request->password));
                    $data->fecha_renovacion = $new_date;
                $data->save();
    
                $renov = new HistorialRenovacion();
                    $renov->fecha = new Carbon();
                    $renov->id_usuario = Auth::user()->id;
                $renov->save();
    
                $response = 'ok';
                $alert = 'success';
                $msj = 'Se ha actualizado la contraseña con éxito';
                $error = '';
            }

        } catch (Exception $ex) {
            $response = 'null';
            $alert = 'error';
            $msj = 'Hubo un problema al ejecutar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $msj, 'error' => $error), 200);
    }
}
