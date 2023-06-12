<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Reporte;

use App\Helpers\mgcp\CuadroCosto\Exportar\CuadroPendienteCierreExport;
use App\Http\Controllers\Controller;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PendienteCierreController extends Controller
{
    private $nombreFormulario = 'Reporte de cuadros pendientes de cierre';

    public function index()
    {
        if (!Auth::user()->tieneRol(54)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $fechaActual = new Carbon();
        return view('mgcp.cuadro-costo.reporte.pendiente-cierre')->with(compact('fechaActual'));
    }

    public function generarArchivo() {
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 5);
        $archivo = new CuadroPendienteCierreExport();
        $archivo->exportar();
    }
}
