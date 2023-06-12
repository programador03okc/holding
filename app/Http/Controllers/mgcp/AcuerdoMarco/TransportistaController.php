<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\mgcp\AcuerdoMarco\Transportista;

class TransportistaController extends Controller {

    /*public function lista() {
        $datos = Transportista::orderBy('empresa', 'asc')->get();
        return response()->json($datos, 200);
    }

    public function registrar(Request $request) {
        if ($request->empresa == '') {
            return response()->json('vacio', 200);
        } else {
            $transportista = Transportista::where('empresa', $request->empresa)->first();
            if (is_null($transportista)) {
                $transportista = new Transportista;
                $transportista->empresa = $request->empresa;
                $transportista->save();
                return response()->json('ok', 200);
            } else {
                return response()->json('existe', 200);
            }
        }
    }*/

}
