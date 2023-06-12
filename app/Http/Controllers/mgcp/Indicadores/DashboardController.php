<?php

namespace App\Http\Controllers\mgcp\Indicadores;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gerencial\Venta;
use App\Models\Indicadores\IndicadorMensual;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(),'Dashboard - Comercial', 1);
        $aniosCdp = DB::select("SELECT DISTINCT DATE_PART('year', cc.fecha_creacion) AS anio FROM mgcp_cuadro_costos.cc ORDER BY anio DESC");
        return view('mgcp.indicadores.dashboard')->with(compact('aniosCdp'));
    }

    public function obtenerIndicadoresCdpPorPeriodo(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso'), 200);
        }
        $pendientesAprobar = CuadroCosto::join('mgcp_oportunidades.oportunidades', 'id_oportunidad', 'oportunidades.id')
            ->whereYear('fecha_creacion', $request->anio)->where('estado_aprobacion', 2)->count();
        $pendientesRegularizar = CuadroCosto::join('mgcp_oportunidades.oportunidades', 'id_oportunidad', 'oportunidades.id')
            ->whereYear('fecha_creacion', $request->anio)->where('estado_aprobacion', 5)->count();
        $ocSinCuadro = OrdenCompraPropiaView::whereYear('fecha_publicacion', $request->anio)->where('estado_oc','!=','RECHAZADA')->whereNull('id_oportunidad')->count();
        $cdpSolAprob24 = OrdenCompraPropiaView::whereYear('fecha_publicacion', $request->anio)->where('sol_aprob_despues_24h', true)->count();
        return response()->json(array(
            'tipo' => 'success', 'pendienteAprobar' => $pendientesAprobar, 'pendientesRegularizar' => $pendientesRegularizar,
            'ocSinCuadro' => $ocSinCuadro, 'cdpSolAprob24' => $cdpSolAprob24
        ), 200);
    }

    public function obtenerMontosAdjudicadosOrdenesPorAnio(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso'), 200);
        }
        $montos = [];
        $meses = [];
        //setlocale(LC_ALL,"es_ES"); 
        Carbon::setLocale('es');
        for ($i = 1; $i <= 12; $i++) {
            $montos[] = number_format(OrdenCompraPropiaView::obtenerMontoMensual($i, $request->anio), 2, '.', '');
            $meses[] = ucfirst(Carbon::now()->setMonth($i)->monthName);
        }
        return response()->json(array('tipo' => 'success', 'montos' => $montos, 'meses' => $meses), 200);
    }

    public function obtenerMontosFacturadosTercerosPorAnio(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso'), 200);
        }
        $montosMeta = [];
        $montosFacturados = [];
        $meses = [];
        //$idVentaInternaGerencial = 28;
        Carbon::setLocale('es');

        //Creación de meses
        for ($i = 1; $i <= 12; $i++) {
            $montosFacturados[] = DB::select("SELECT COALESCE(ABS(SUM((CASE WHEN id_tipo_documento IN (1,2) THEN -1 ELSE 1 END)*importe*tc)),0)/1.18 AS monto FROM gerencial.venta WHERE 
            EXTRACT(month FROM fecha)=? AND EXTRACT(year FROM fecha)=? AND estado=1 AND nombre_vendedor!=28 AND id_tipo_documento IN (1,2,3)", [$i, $request->anio])[0]->monto; //$sumaMontos;
            $meses[] = ucfirst(Carbon::now()->setMonth($i)->monthName);
        }
        //Lectura de metas por anio
        $meta = IndicadorMensual::whereYear('fecha', $request->anio)->latest()->first();
        $montosMeta[] = $meta->ene;
        $montosMeta[] = $meta->feb;
        $montosMeta[] = $meta->mar;
        $montosMeta[] = $meta->abr;
        $montosMeta[] = $meta->may;
        $montosMeta[] = $meta->jun;
        $montosMeta[] = $meta->jul;
        $montosMeta[] = $meta->ago;
        $montosMeta[] = $meta->set;
        $montosMeta[] = $meta->oct;
        $montosMeta[] = $meta->nov;
        $montosMeta[] = $meta->dic;
        return response()->json(array('tipo' => 'success', 'montosMeta' => $montosMeta, 'montosFacturados' => $montosFacturados, 'meses' => $meses), 200);
    }
}
