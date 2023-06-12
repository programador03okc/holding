<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra;

use App\Helpers\mgcp\AcuerdoMarco\NuevaVistaProformaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualNuevaVistaHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\FleteProforma;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaProveedor;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GCINuevaVistaController extends Controller
{
    private $nombreFormulario = 'Proforma gran compra individual - Nueva vista';

    public function index()
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $departamentos = Departamento::orderBy('nombre')->get();
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $catalogos = Catalogo::join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')->where('activo', true)
            ->orderBy('catalogos.id', 'asc')->select(['catalogos.id', 'catalogos.descripcion AS catalogo', 'acuerdo_marco.descripcion AS acuerdo_marco'])->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_gran_compra ORDER BY estado");
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.productos_am WHERE id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria) ORDER BY marca");
        $proveedores = OrdenCompraPublicaProveedor::orderBy('nombre', 'asc')->get();
        $tcUsd = TcSbs::orderBy('fecha', 'desc')->first()->precio;
        $fechaActual = new Carbon();
        $tipoProforma = 2;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.proforma.individual.nueva-vista', get_defined_vars());
    }

    public function obtenerProformas(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $helper= new ProformaIndividualNuevaVistaHelper($request);
        if (!is_null($request->criterio)) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->criterio);
        }
        return response()->json(array('body' => $helper->generarLista(Auth::user()), 'footer' => $helper->generarPaginacionProformas()), 200);
    }
}
