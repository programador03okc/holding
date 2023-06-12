<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\mgcp\CuadroCosto\Responsable;
use Mail;
use Carbon\Carbon;

class ResponsableController extends Controller {

    public function __construct() {
    }
    
    public function agregar(Request $request) {
        $responsable= new Responsable;
        $responsable->id_cc=$request->idCuadro;
        $responsable->id_responsable=0;
        $responsable->porcentaje=0;
        $responsable->save();
        return response()->json(array('id' => $responsable->id), 200);
    }
    
    public function eliminar(Request $request) {
        $responsable = Responsable::find($request->id);
        $responsable->delete();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Registro eliminado.'));
    }
    
    public function actualizar(Request $request) {
        $responsable= Responsable::find($request->id);
        $responsable->id_responsable=$request->idCorporativo;
        $responsable->porcentaje=$request->porcentaje;
        $responsable->save();
        return response()->json(array('tipo' => 'success','mensaje'=>'Se ha registrado el cambio'), 200);
    }
}
