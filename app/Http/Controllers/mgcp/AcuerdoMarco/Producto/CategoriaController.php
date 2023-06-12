<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Producto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;

class CategoriaController extends Controller
{
    public function listarPorCatalogo(Request $request)
    {
        if ($request->tipoId == 'mgcp') {
            $categorias = Categoria::where('id_catalogo', $request->idCatalogo)->orderBy('descripcion', 'asc')->get();
        } else {
            $catalogo = Catalogo::where('id_pc', $request->idCatalogo)->first();
            $categorias = Categoria::where('id_catalogo', $catalogo->id)->orderBy('descripcion', 'asc')->get();
        }
        return response()->json($categorias, 200);
    }
}
