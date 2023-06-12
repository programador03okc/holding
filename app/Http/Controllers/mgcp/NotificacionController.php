<?php

namespace App\Http\Controllers\mgcp;

use App\Models\User;
use App\Models\mgcp\Usuario\Notificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class NotificacionController extends Controller 
{
    private $nombreFormulario = 'Notificaciones de usuario';
    
    public function ver($id) {
        $notificacion = Notificacion::find($id);
        if ($notificacion == null || $notificacion->id_usuario != Auth::user()->id) {
            return redirect()->route('home');
        } else {
            $notificacion->leido = 1;
            $notificacion->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'ID: ' . $notificacion->id);
            return redirect($notificacion->url);
        }
    }
    
    public function eliminar(Request $request)
    {
        $notificacion = Notificacion::find($request->id);
        if ($notificacion == null || $notificacion->id_usuario != Auth::user()->id) {
            return response()->json(array('tipo' => "danger", 'mensaje' => 'No puede eliminar una notificación que no le fue asignada'), 200);
        } else {
            DB::beginTransaction();
                $notificacion->delete();
                LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $notificacion->getTable(), $notificacion);
            DB::commit();
        }
    }
    
    public function cantidadNoLeidas()
    {
        $data = Notificacion::where('id_usuario',Auth::user()->id)->where('leido',false)->get();
        return response()->json(array('tipo' =>"success", 'mensaje' => $data->count()), 200);
    }
    
    public function lista() {
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.usuario.notificacion');
    }

    public function dataLista(Request $request) {
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        $data = Notificacion::where('id_usuario',Auth::user()->id)->select(['id','mensaje','fecha','url','leido']);
        return datatables($data)->rawColumns(['mensaje'])->toJson();
    }
}
