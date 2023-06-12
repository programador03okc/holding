<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaFiltrosHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra\GranCompraIndividualController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\FleteProforma;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GCIVistaAnteriorController extends Controller
{
    private $nombreFormulario = 'Proforma gran compra individual - Vista anterior';

    public function index()
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        
        $departamentos = Departamento::orderBy('nombre')->get();
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_gran_compra ORDER BY estado");
        $catalogos = Catalogo::join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')->where('activo', true)
            ->orderBy('catalogos.id', 'asc')->select(['catalogos.id', 'catalogos.descripcion AS catalogo', 'acuerdo_marco.descripcion AS acuerdo_marco'])->get();
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.productos_am WHERE id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria) ORDER BY marca");
        $fechaActual = new Carbon();
        $tipoProforma = 2;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.proforma.individual.vista-anterior', get_defined_vars());
    }

    public function generarListaParaDatatable(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        ProformaFiltrosHelper::actualizar($request);
        $proformas = GranCompra::listar($request);
        return datatables($proformas)->rawColumns(['entidad.semaforo', 'empresa.semaforo', 'software_educativo', 'requerimiento'])->toJson();
    }
}
