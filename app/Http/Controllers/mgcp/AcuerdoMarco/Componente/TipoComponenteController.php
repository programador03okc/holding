<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Componente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoComponenteController extends Controller
{
    public function index()
    {
        return view('mgcp.acuerdo-marco.proforma.componente.tipo-componente');
    }
}
