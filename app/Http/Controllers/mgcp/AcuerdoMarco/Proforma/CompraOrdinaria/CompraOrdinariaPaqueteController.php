<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPortalHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Proforma\ComentarioCompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\FleteProforma;
use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ProformaPaqueteController;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Paquete;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class CompraOrdinariaPaqueteController extends Controller
{
    private $nombreFormulario = 'Proforma compra ordinaria por paquete';

    public function index()
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $tipoProforma = 1;
        $fechaActual = new Carbon();
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $estados = Paquete::where('tipo', $tipoProforma)->select(['estado'])->distinct()->orderBy('estado', 'asc')->get();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.proforma.paquete.vista', get_defined_vars());
    }
}
