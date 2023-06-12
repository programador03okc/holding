<?php

namespace App\Http\Controllers\mgcp;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller
{
    public function cambiarPassword()
    {
        return view('mgcp.perfil.cambiar_password');
    }

    public function actualizarPassword(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'password' => [
                'required', 'confirmed', 'min:6',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/'  // must contain a special character
            ]
        ]);

        if ($validar->fails()) {
            //$request->session()->flash('alert-danger', 'Err');
            return Redirect::route('mgcp.perfil.cambiar-password')->withErrors($validar)
                ->withInput();
        } else {
            try {
                $user = User::find(Auth::user()->id);
                $user->password = bcrypt(trim($request->password));
                $user->save();
                $request->session()->flash('alert-success', 'Se ha cambiado su contraseña');
            } catch (Exception $ex) {
                $request->session()->flash('alert-danger', 'Hubo un problema al cambiar la contraseña. Por favor inténtelo de nuevo');
            } finally {
                return Redirect::route('mgcp.perfil.cambiar-password', trim($request->id_usuario));
            }
        }
    }







    /*public function getEditar()
    {
        return view('auth/editar_perfil');
    }*/

    /*public function getCambiarFoto()
    {
        return view('auth/cambiar_foto');
    }*/

    /*private function limpiarNombre($nombre)
    {
        $nombre = str_replace(' ', '-', $nombre);
        $nombre = preg_replace('/[^A-Za-z0-9.\-]/', '', $nombre);
        return preg_replace('/-+/', '-', $nombre);
    }*/

    /*public function postCambiarFoto(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'foto' => 'required|mimes:jpg,png,jpeg,gif,bmp'
        ]);
        if ($validar->fails()) {
            return redirect()->route('perfil.getCambiarFoto')
                ->withErrors($validar)
                ->withInput();
        } else {
            $archivo = $request->file('foto');
            $nombreFinal = $this->limpiarNombre($archivo->getClientOriginalName());


            $ruta = 'img/avatar/' . Auth::user()->id;
            if (!is_dir($ruta)) {
                File::makeDirectory($ruta);
            }
            $archivo->move($ruta, $nombreFinal);
            $usuario = User::find(Auth::user()->id);
            $usuario->foto_perfil = $nombreFinal;
            $usuario->save();
            $request->session()->flash('alert-success', 'Foto cambiada correctamente.');
            return redirect()->route('perfil.getCambiarFoto');
        }
    }*/



    /*public function postEditar(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required'
        ]);
        if ($validar->fails()) {
            return \Redirect::route('perfil.getEditar')->withErrors($validar)
                ->withInput();
        } else {
            try {
                $user = User::find(Auth::user()->id);
                $user->name = trim($request->nombre);
                $user->cargo = trim($request->cargo);
                $user->celular = trim($request->telefono);
                $user->save();
                $request->session()->flash('alert-success', 'Su perfil se ha actualizado.');
            } catch (\PDOException $ex) {
                $request->session()->flash('alert-danger', 'Hubo un error al actualizar su perfil. Por favor inténtelo de nuevo.');
            } finally {
                return \Redirect::route('perfil.getEditar');
            }
        }
    }*/
}
