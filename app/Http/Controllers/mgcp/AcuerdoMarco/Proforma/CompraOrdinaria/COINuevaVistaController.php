<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;

use App\Exports\FormatoProformaExport;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaFiltrosHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualNuevaVistaHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\FleteProforma;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaProveedor;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class COINuevaVistaController extends Controller
{
    private $nombreFormulario = 'Proforma compra ordinaria individual - Nueva vista';

    public function index()
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);

        $departamentos = Departamento::orderBy('nombre')->get();
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        $catalogos = Catalogo::join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', 'acuerdo_marco.id')->where('activo', true)
            ->orderBy('catalogos.id', 'asc')->select(['catalogos.id', 'catalogos.descripcion AS catalogo', 'acuerdo_marco.descripcion AS acuerdo_marco'])->get();
        $estados = DB::select("SELECT distinct estado from mgcp_acuerdo_marco.proformas_compra_ordinaria ORDER BY estado");
        $marcas = DB::select("SELECT DISTINCT marca FROM mgcp_acuerdo_marco.productos_am WHERE id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria) ORDER BY marca");
        $fechaActual = new Carbon();
        $tipoProforma = 1;
        $proveedores = OrdenCompraPublicaProveedor::orderBy('nombre', 'asc')->get();
        $tcUsd = TcSbs::orderBy('fecha', 'desc')->first()->precio;
        return view('mgcp.acuerdo-marco.proforma.individual.nueva-vista', get_defined_vars());
    }

    public function obtenerProformas(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return view('mgcp.usuario.sin_permiso');
        }
        /*Se obtienen los requerimientos de acuerdo a los filtros seleccionados*/
        ProformaFiltrosHelper::actualizar($request);
        $helper= new ProformaIndividualNuevaVistaHelper($request);

        if (!is_null($request->criterio)) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->criterio);
        }
        return response()->json(array('body' => $helper->generarLista(Auth::user()), 'footer' => $helper->generarPaginacionProformas()), 200);
    }

    //Método para la nueva vista de proformas
    public function ingresarFletePorLote(Request $request)
    {
        if (!Auth::user()->tieneRol(37)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para acceder a este formulario'), 200);
        }
        $proformas =CompraOrdinaria::generarConsultaProformas($request)->where('estado', 'PENDIENTE')->where('requiere_flete',true)->whereNull('costo_envio_publicar')->get();
        $fecha = new Carbon;
        foreach ($proformas as $proforma) {
            $proforma->costo_envio_publicar = floatval(($proforma->moneda_ofertada == 'PEN' ? 1 : $proforma->tipo_cambio)*$proforma->precio_publicar)-0.01;  //rand($flete->flete_minimo, $flete->flete_maximo);;
            $proforma->id_ultimo_usuario = Auth::user()->id;
            $proforma->tipo_carga = 'MANUAL';
            $proforma->fecha_cotizacion = $fecha;
            $proforma->save();
        }
        $cantidad = $proformas->count();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 14, null, null, null, 'Proformas afectadas: ' . $cantidad);
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha ingresado el flete a ' . $cantidad . ($cantidad == 1 ? ' proforma' : ' proformas')), 200);
    }

    public function generarFletePorLoteMasivo()
    {
        $fecha = new Carbon();
        $ini = $fecha->addMonths(-1)->format('Y-m-d');
        $fin = $fecha->addMonths(1)->format('Y-m-d');
        $day = date('Y-m-d');

        $proformas = CompraOrdinaria::generarConsultaProformasAutomatica($ini, $fin)->where('estado', 'PENDIENTE')
                                    ->where('fecha_limite', $day)->where('requiere_flete', true)->whereNull('costo_envio_publicar')->get();
        foreach ($proformas as $proforma) {
            $proforma->costo_envio_publicar = floatval(($proforma->moneda_ofertada == 'PEN' ? 1 : $proforma->tipo_cambio) * $proforma->precio_publicar) - 0.01;
            $proforma->id_ultimo_usuario = 24;
            $proforma->tipo_carga = 'MASIVO';
            $proforma->fecha_cotizacion = $fecha;
            $proforma->save();
        }
        die('***FIN DEL PROCESO. TOTAL DE PRODUCTOS: ' . $proformas->count() . '***');
    }
}
