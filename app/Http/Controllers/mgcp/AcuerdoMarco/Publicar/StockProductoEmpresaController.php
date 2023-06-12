<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockProductoEmpresaController extends Controller
{
    public function index()
    {
        if (!Auth::user()->tieneRol(52)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.publicar.stock-productos', get_defined_vars());
    }
}
