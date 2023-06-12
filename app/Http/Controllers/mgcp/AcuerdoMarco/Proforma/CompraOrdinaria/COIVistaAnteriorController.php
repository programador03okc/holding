<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaFiltrosHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\FleteProforma;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class COIVistaAnteriorController extends Controller
{
    private $nombreFormulario = 'Proforma compra ordinaria individual - Vista anterior';

    public function index()
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        
        $departamentos = Departamento::orderBy('nombre')->get();
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_compra_ordinaria ORDER BY estado");
        $catalogos = Catalogo::join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')->where('activo', true)
            ->orderBy('catalogos.id', 'asc')->select(['catalogos.id', 'catalogos.descripcion AS catalogo', 'acuerdo_marco.descripcion AS acuerdo_marco'])->get();
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.productos_am WHERE id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria) ORDER BY marca");
        $fechaActual = new Carbon();
        $tipoProforma = 1;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.proforma.individual.vista-anterior', get_defined_vars());
    }

    public function generarListaParaDatatable(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }
        ProformaFiltrosHelper::actualizar($request);
        $proformas = CompraOrdinaria::listar($request);
        return datatables($proformas)->rawColumns(['entidad.semaforo', 'empresa.semaforo', 'software_educativo', 'requerimiento'])->toJson();
    }

    public function ingresarFletePorLote(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }
        //$tipoCambio=floatval(TipoCambio::first()->tipo_cambio)-0.15;
        $proformas = CompraOrdinaria::listar($request)->where('estado', 'PENDIENTE')->where('requiere_flete',true)->whereNull('costo_envio_publicar')->get();
        $fecha=new Carbon();
        foreach ($proformas as $proforma) {
            //$flete = FleteProforma::where('monto_hasta', '!=', 0)->where('monto_hasta', '>=', (int) ($proforma->precio_publicar ?? 0))->orderBy('monto_hasta', 'asc')->first() ?? FleteProforma::where('monto_hasta', 0)->first();
            //$proforma->costo_envio_publicar = (($proforma->moneda_ofertada =='PEN' ? 1 : $tipoCambio)*($proforma->precio_publicar ?? 0)) * 0.9999;  //rand($flete->flete_minimo, $flete->flete_maximo);
            $proforma->costo_envio_publicar =floatval(($proforma->moneda_ofertada =='PEN' ? 1 : $proforma->tipo_cambio)*$proforma->precio_publicar)-0.01; 
            $proforma->id_ultimo_usuario = Auth::user()->id;
            $proforma->fecha_cotizacion = $fecha;
            $proforma->save();
        }
        $cantidad = $proformas->count();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha ingresado el flete a ' . $cantidad . ($cantidad == 1 ? ' proforma' : ' proformas')), 200);
    }
}
