<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Producto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;

class CatalogoController extends Controller
{

    public function listarPorAcuerdo(Request $request)
    {
        if ($request->tipoId == 'mgcp') {
            $catalogos = Catalogo::where('id_acuerdo_marco', $request->idAcuerdo)->orderBy('descripcion', 'asc')->get();
        } else {
            $acuerdo = AcuerdoMarco::where('id_pc', $request->idAcuerdo)->first();
            $catalogos = Catalogo::where('id_acuerdo_marco', $acuerdo->id)->orderBy('descripcion', 'asc')->get();
        }
        return response()->json($catalogos, 200);
    }
}
