<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\Exportar\EntidadesProforma;
use App\Helpers\mgcp\Exportar\ProformaExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\ProformaView;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExportarController extends Controller
{
    public function index()
    {
        if (!Auth::user()->tieneRol(130)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $catalogos = Catalogo::whereRaw('id_acuerdo_marco IN (SELECT acuerdo_marco.id FROM mgcp_acuerdo_marco.acuerdo_marco WHERE activo=true)')->orderBy('descripcion', 'asc')->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_compra_ordinaria ORDER BY estado");
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.proforma.exportar')->with(compact('empresas', 'estados', 'catalogos'));
    }

    public function entidades()
    {
        if (!Auth::user()->tieneRol(130)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('mgcp.acuerdo-marco.proforma.exportar-entidades')->with(compact('departamentos'));
    }

    public function generarArchivo(Request $request)
    {
        if (!Auth::user()->tieneRol(130)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $data = ProformaView::whereIn('id_empresa', $request->empresa)->whereIn('id_catalogo', $request->catalogo)
            ->whereIn('estado', $request->estado)
            ->whereBetween('fecha_emision',[Carbon::createFromFormat('d-m-Y', $request->fechaEmisionDesde)->toDateString(),Carbon::createFromFormat('d-m-Y', $request->fechaEmisionHasta)->toDateString()]);
        if (count($request->comentario) == 1) {
            $data = $request->comentario[0] == 0 ? $data->whereNull('ultimo_comentario') : $data->whereNotNull('ultimo_comentario');
        }
        if (count($request->tipo) == 1) {
            $data = $data->where('tipo',$request->tipo[0]);
        }
        LogActividad::registrar(Auth::user(),  'Exportar proformas de Acuerdo marco', 5);
        $exportar = new ProformaExport();
        $exportar->exportar($data->orderBy('fecha_limite', 'desc')->get());
    }

    public function generarEntidades(Request $request)
    {
        if (!Auth::user()->tieneRol(130)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $data = CompraOrdinaria::whereIn('id_departamento', $request->departamento)->whereBetween('fecha_emision', [
            Carbon::createFromFormat('d-m-Y', $request->fechaEmisionDesde)->toDateString(), Carbon::createFromFormat('d-m-Y', $request->fechaEmisionHasta)->toDateString()
        ]);
        LogActividad::registrar(Auth::user(),  'Exportar entidades', 5);
        $exportar = new EntidadesProforma();
        $exportar->exportar($data->get());
    }
}
