<?php

namespace App\Http\Controllers\mgcp\Indicadores;

use App\Http\Controllers\Controller;
use App\Models\Administracion\Periodo;
use App\Models\Gerencial\CentroCostoModel;
use App\Models\Gerencial\DivisionModel;
use App\Models\Gerencial\EmpresaModel;
use App\Models\Gerencial\PeriodoModel;
use App\Models\Gerencial\VendedorModel;
use App\Models\Gerencial\VentaModel;
use App\Models\Indicadores\IndicadorDivision;
use App\Models\Indicadores\IndicadorDivisionDetalle;
use App\Models\Indicadores\IndicadorMensual;
use App\Models\Indicadores\IndicadorVendedor;
use App\Models\Indicadores\IndicadorVendedorDetalle;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndicadorController extends Controller
{
    public function __construct()
    {
       
    }

    public function viewDashboardContabilidad(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Dashboard de ventas - Contabilidad', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.dashboard.contabilidad', compact('periodo'));
    }

    public function nuevaEmpresaMensual(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Formulario de Metas Comerciales', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.meta.meta_empresa_mensual', compact('periodo'));
    }

    public function nuevaDivisionMensual(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Formulario de Metas por División', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.meta.meta_division_mensual', compact('periodo'));
    }

    public function nuevaCorporativoMensual(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Formulario de Metas por Corporativo', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.meta.meta_corporativo_mensual', compact('periodo'));
    }

    public function guardarMensual(Request $request)
    {
       
        try {
            if (!Auth::user()->tieneRol(132)) {
                return response()->json(array('response' => 'eror', 'alert' => 'error', 'message' => 'Sin permiso para guardar datos', 'excepcion' => ''), 200);
            }

            $data = new IndicadorMensual();
                $data->fecha = new Carbon();
                $data->id_periodo = $request->periodo;
                $data->ene = $request->ene;
                $data->feb = $request->feb;
                $data->mar = $request->mar;
                $data->abr = $request->abr;
                $data->may = $request->may;
                $data->jun = $request->jun;
                $data->jul = $request->jul;
                $data->ago = $request->ago;
                $data->set = $request->set;
                $data->oct = $request->oct;
                $data->nov = $request->nov;
                $data->dic = $request->dic;
            $data->save();
            
            LogActividad::registrar(Auth::user(), 'Registro de Metas Comerciales', 4, $data->getTable(), null, $data);
    
            $response = 'ok';
            $alert = 'success';
            $message = 'Se ha registrado las metas mensuales';
            $excepcion ='';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo.';
            $excepcion = $ex->getMessage();
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'excepcion' => $excepcion), 200);
    }

    public function guardarDivision(Request $request)
    {
       
        try {
            if (!Auth::user()->tieneRol(132)) {
                return response()->json(array('response' => 'eror', 'alert' => 'error', 'message' => 'Sin permiso para guardar datos','excepcion'=>''), 200);
            }

            $head = new IndicadorDivision();
                $head->fecha = new Carbon();
                $head->id_periodo = $request->periodo;
            $head->save();

            $item = count($request->divi);
            $divi = $request->divi;
            $dene = $request->ene;
            $dfeb = $request->feb;
            $dmar = $request->mar;
            $dabr = $request->abr;
            $dmay = $request->may;
            $djun = $request->jun;
            $djul = $request->jul;
            $dago = $request->ago;
            $dset = $request->set;
            $doct = $request->oct;
            $dnov = $request->nov;
            $ddic = $request->dic;

            for ($i = 0; $i < $item; $i++) { 
                $data = new IndicadorDivisionDetalle();
                    $data->id_kpi_division = $head->id_kpi_division;
                    $data->id_centro_costo = $divi[$i];
                    $data->ene = $dene[$i];
                    $data->feb = $dfeb[$i];
                    $data->mar = $dmar[$i];
                    $data->abr = $dabr[$i];
                    $data->may = $dmay[$i];
                    $data->jun = $djun[$i];
                    $data->jul = $djul[$i];
                    $data->ago = $dago[$i];
                    $data->set = $dset[$i];
                    $data->oct = $doct[$i];
                    $data->nov = $dnov[$i];
                    $data->dic = $ddic[$i];
                $data->save();
                LogActividad::registrar(Auth::user(), 'Registro de Metas por División', 4, $data->getTable(), null, $data);
            }
    
            $response = 'ok';
            $alert = 'success';
            $message = 'Se ha registrado las metas por división';
            $excepcion='';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo.';
            $excepcion=$ex->getMessage();
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message,'excepcion'=>$excepcion), 200);
    }

    public function guardarVendedor(Request $request)
    {
       
        try {
            if (!Auth::user()->tieneRol(132)) {
                return response()->json(array('response' => 'eror', 'alert' => 'error', 'message' => 'Sin permiso para guardar datos', 'excepcion' => ''), 200);
            }

            $head = new IndicadorVendedor();
                $head->fecha = new Carbon();
                $head->id_periodo = $request->periodo;
            $head->save();

            $item = count($request->vend);
            $dven = $request->vend;
            $dene = $request->ene;
            $dfeb = $request->feb;
            $dmar = $request->mar;
            $dabr = $request->abr;
            $dmay = $request->may;
            $djun = $request->jun;
            $djul = $request->jul;
            $dago = $request->ago;
            $dset = $request->set;
            $doct = $request->oct;
            $dnov = $request->nov;
            $ddic = $request->dic;

            for ($i = 0; $i < $item; $i++) { 
                $data = new IndicadorVendedorDetalle();
                    $data->id_kpi_vendedor = $head->id_kpi_vendedor;
                    $data->id_vendedor = $dven[$i];
                    $data->ene = $dene[$i];
                    $data->feb = $dfeb[$i];
                    $data->mar = $dmar[$i];
                    $data->abr = $dabr[$i];
                    $data->may = $dmay[$i];
                    $data->jun = $djun[$i];
                    $data->jul = $djul[$i];
                    $data->ago = $dago[$i];
                    $data->set = $dset[$i];
                    $data->oct = $doct[$i];
                    $data->nov = $dnov[$i];
                    $data->dic = $ddic[$i];
                $data->save();
            }
    
            $response = 'ok';
            $alert = 'success';
            $message = 'Se ha registrado las metas por corporativo';
            $excepcion ='';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo.';
            $excepcion = $ex->getMessage();
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'excepcion' => $excepcion), 200);
    }

    public function mostrarMensual()
    {
        $query = IndicadorMensual::orderBy('created_at', 'desc')->get();
        $data = array();

        foreach ($query as $key) {
            $anio = Periodo::findOrFail($key->id_periodo);
            $data[] = [
                'anio'  => $anio->descripcion,
                'ene'   => $key->ene,
                'feb'   => $key->feb,
                'mar'   => $key->mar,
                'abr'   => $key->abr,
                'may'   => $key->may,
                'jun'   => $key->jun,
                'jul'   => $key->jul,
                'ago'   => $key->ago,
                'set'   => $key->set,
                'oct'   => $key->oct,
                'nov'   => $key->nov,
                'dic'   => $key->dic,
            ];
        }
        return response()->json($data, 200);
    }

    public function mostrarDivision()
    {
        $sql = IndicadorDivision::latest()->first();
        $query = IndicadorDivisionDetalle::where('id_kpi_division', $sql->id_kpi_division)->get();
        $data = array();

        foreach ($query as $key) {
            $ccs = CentroCostoModel::findOrFail($key->id_centro_costo);
            $data[] = [
                'division' => $ccs->descripcion,
                'ene'   => $key->ene,
                'feb'   => $key->feb,
                'mar'   => $key->mar,
                'abr'   => $key->abr,
                'may'   => $key->may,
                'jun'   => $key->jun,
                'jul'   => $key->jul,
                'ago'   => $key->ago,
                'set'   => $key->set,
                'oct'   => $key->oct,
                'nov'   => $key->nov,
                'dic'   => $key->dic,
            ];
        }
        return response()->json($data, 200);
    }

    public function mostrarDivisionId(Request $request)
    {
        $sql = IndicadorDivision::latest()->first();
        $query = IndicadorDivisionDetalle::where([['id_kpi_division', $sql->id_kpi_division], ['id_centro_costo', $request->value]])->get();
        $data = array();

        foreach ($query as $key) {
            $ccs = CentroCostoModel::findOrFail($request->value);
            $data[] = [
                'ene'   => $key->ene,
                'feb'   => $key->feb,
                'mar'   => $key->mar,
                'abr'   => $key->abr,
                'may'   => $key->may,
                'jun'   => $key->jun,
                'jul'   => $key->jul,
                'ago'   => $key->ago,
                'set'   => $key->set,
                'oct'   => $key->oct,
                'nov'   => $key->nov,
                'dic'   => $key->dic,
            ];
        }
        return response()->json(array('data' => $data), 200);
    }

    public function listaVendedor()
    {
        $data = VendedorModel::where('estado', 1)->orderBy('nombre')->get();
        return response()->json($data, 200);
    }

    public function listaDivision()
    {
        $data = CentroCostoModel::where([['estado', 1], ['id_padre', 66], ['version', 2]])->orderBy('id_centro_costo', 'asc')->get();
        return response()->json($data, 200);
    }

    public function ventaEmpresa(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Reporte de Ventas por Empresa - Módulo Gerencial', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.ventas_empresa', compact('periodo'));
    }

    public function ventaDivision(Request $request)
    {
        if (!Auth::user()->tieneRol(132)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), 'Reporte de Ventas por División - Módulo Gerencial', 1);
        $periodo = Periodo::orderBy('descripcion', 'desc')->get();
        $cen_cos = CentroCostoModel::where([['estado', 1], ['id_padre', 66], ['version', 2]])->orderBy('descripcion', 'desc')->get();
        return view('mgcp.indicadores.ventas_division', compact('periodo', 'cen_cos'));
    }

    public function busqueda(Request $request)
    {
        $ini_m1 = $this->primerDia(1, $request->periodo);
        $fin_m1 = $this->ultimoDia(1, $request->periodo);
        $ini_m2 = $this->primerDia(2, $request->periodo);
        $fin_m2 = $this->ultimoDia(2, $request->periodo);
        $ini_m3 = $this->primerDia(3, $request->periodo);
        $fin_m3 = $this->ultimoDia(3, $request->periodo);
        $ini_m4 = $this->primerDia(4, $request->periodo);
        $fin_m4 = $this->ultimoDia(4, $request->periodo);
        $ini_m5 = $this->primerDia(5, $request->periodo);
        $fin_m5 = $this->ultimoDia(5, $request->periodo);
        $ini_m6 = $this->primerDia(6, $request->periodo);
        $fin_m6 = $this->ultimoDia(6, $request->periodo);
        $ini_m7 = $this->primerDia(7, $request->periodo);
        $fin_m7 = $this->ultimoDia(7, $request->periodo);
        $ini_m8 = $this->primerDia(8, $request->periodo);
        $fin_m8 = $this->ultimoDia(8, $request->periodo);
        $ini_m9 = $this->primerDia(9, $request->periodo);
        $fin_m9 = $this->ultimoDia(9, $request->periodo);
        $ini_m10 = $this->primerDia(10, $request->periodo);
        $fin_m10 = $this->ultimoDia(10, $request->periodo);
        $ini_m11 = $this->primerDia(11, $request->periodo);
        $fin_m11 = $this->ultimoDia(11, $request->periodo);
        $ini_m12 = $this->primerDia(12, $request->periodo);
        $fin_m12 = $this->ultimoDia(12, $request->periodo);

        if ($request->type == 'company') {
            $company = EmpresaModel::where('estado', 1)->orderBy('id_empresa', 'asc')->get();

            foreach ($company as $key) {
                $ve_int_m1 = $this->importeVentasEmpresa($key->id_empresa, $ini_m1, $fin_m1, 1);
                $ve_int_m2 = $this->importeVentasEmpresa($key->id_empresa, $ini_m2, $fin_m2, 1);
                $ve_int_m3 = $this->importeVentasEmpresa($key->id_empresa, $ini_m3, $fin_m3, 1);
                $ve_int_m4 = $this->importeVentasEmpresa($key->id_empresa, $ini_m4, $fin_m4, 1);
                $ve_int_m5 = $this->importeVentasEmpresa($key->id_empresa, $ini_m5, $fin_m5, 1);
                $ve_int_m6 = $this->importeVentasEmpresa($key->id_empresa, $ini_m6, $fin_m6, 1);
                $ve_int_m7 = $this->importeVentasEmpresa($key->id_empresa, $ini_m7, $fin_m7, 1);
                $ve_int_m8 = $this->importeVentasEmpresa($key->id_empresa, $ini_m8, $fin_m8, 1);
                $ve_int_m9 = $this->importeVentasEmpresa($key->id_empresa, $ini_m9, $fin_m9, 1);
                $ve_int_m10 = $this->importeVentasEmpresa($key->id_empresa, $ini_m10, $fin_m10, 1);
                $ve_int_m11 = $this->importeVentasEmpresa($key->id_empresa, $ini_m11, $fin_m11, 1);
                $ve_int_m12 = $this->importeVentasEmpresa($key->id_empresa, $ini_m12, $fin_m12, 1);

                $dataInterna = array(
                    'ene' => $ve_int_m1, 'feb' => $ve_int_m2, 'mar' => $ve_int_m3, 'abr' => $ve_int_m4,
                    'may' => $ve_int_m5, 'jun' => $ve_int_m6, 'jul' => $ve_int_m7, 'ago' => $ve_int_m8,
                    'set' => $ve_int_m9, 'oct' => $ve_int_m10, 'nov' => $ve_int_m11, 'dic' => $ve_int_m12
                );

                $ve_ext_m1 = $this->importeVentasEmpresa($key->id_empresa, $ini_m1, $fin_m1, 2);
                $ve_ext_m2 = $this->importeVentasEmpresa($key->id_empresa, $ini_m2, $fin_m2, 2);
                $ve_ext_m3 = $this->importeVentasEmpresa($key->id_empresa, $ini_m3, $fin_m3, 2);
                $ve_ext_m4 = $this->importeVentasEmpresa($key->id_empresa, $ini_m4, $fin_m4, 2);
                $ve_ext_m5 = $this->importeVentasEmpresa($key->id_empresa, $ini_m5, $fin_m5, 2);
                $ve_ext_m6 = $this->importeVentasEmpresa($key->id_empresa, $ini_m6, $fin_m6, 2);
                $ve_ext_m7 = $this->importeVentasEmpresa($key->id_empresa, $ini_m7, $fin_m7, 2);
                $ve_ext_m8 = $this->importeVentasEmpresa($key->id_empresa, $ini_m8, $fin_m8, 2);
                $ve_ext_m9 = $this->importeVentasEmpresa($key->id_empresa, $ini_m9, $fin_m9, 2);
                $ve_ext_m10 = $this->importeVentasEmpresa($key->id_empresa, $ini_m10, $fin_m10, 2);
                $ve_ext_m11 = $this->importeVentasEmpresa($key->id_empresa, $ini_m11, $fin_m11, 2);
                $ve_ext_m12 = $this->importeVentasEmpresa($key->id_empresa, $ini_m12, $fin_m12, 2);

                $dataExterna = array(
                    'ene' => $ve_ext_m1, 'feb' => $ve_ext_m2, 'mar' => $ve_ext_m3, 'abr' => $ve_ext_m4,
                    'may' => $ve_ext_m5, 'jun' => $ve_ext_m6, 'jul' => $ve_ext_m7, 'ago' => $ve_ext_m8,
                    'set' => $ve_ext_m9, 'oct' => $ve_ext_m10, 'nov' => $ve_ext_m11, 'dic' => $ve_ext_m12
                );

                $data[] = ['empresa' => $key->nombre, 'interna' => $dataInterna, 'externa' => $dataExterna];
            }
        } else if ($request->type == 'division') {
            $division = CentroCostoModel::where([['estado', 1], ['id_padre', 66], ['version', 2]])->orderBy('id_centro_costo', 'asc')->get();

            foreach ($division as $row) {
                $ve_int_m1 = $this->importeVentasDivision($row->codigo, $ini_m1, $fin_m1, 1);
                $ve_int_m2 = $this->importeVentasDivision($row->codigo, $ini_m2, $fin_m2, 1);
                $ve_int_m3 = $this->importeVentasDivision($row->codigo, $ini_m3, $fin_m3, 1);
                $ve_int_m4 = $this->importeVentasDivision($row->codigo, $ini_m4, $fin_m4, 1);
                $ve_int_m5 = $this->importeVentasDivision($row->codigo, $ini_m5, $fin_m5, 1);
                $ve_int_m6 = $this->importeVentasDivision($row->codigo, $ini_m6, $fin_m6, 1);
                $ve_int_m7 = $this->importeVentasDivision($row->codigo, $ini_m7, $fin_m7, 1);
                $ve_int_m8 = $this->importeVentasDivision($row->codigo, $ini_m8, $fin_m8, 1);
                $ve_int_m9 = $this->importeVentasDivision($row->codigo, $ini_m9, $fin_m9, 1);
                $ve_int_m10 = $this->importeVentasDivision($row->codigo, $ini_m10, $fin_m10, 1);
                $ve_int_m11 = $this->importeVentasDivision($row->codigo, $ini_m11, $fin_m11, 1);
                $ve_int_m12 = $this->importeVentasDivision($row->codigo, $ini_m12, $fin_m12, 1);

                $dataInterna = array(
                    'ene' => $ve_int_m1, 'feb' => $ve_int_m2, 'mar' => $ve_int_m3, 'abr' => $ve_int_m4,
                    'may' => $ve_int_m5, 'jun' => $ve_int_m6, 'jul' => $ve_int_m7, 'ago' => $ve_int_m8,
                    'set' => $ve_int_m9, 'oct' => $ve_int_m10, 'nov' => $ve_int_m11, 'dic' => $ve_int_m12
                );

                $ve_ext_m1 = $this->importeVentasDivision($row->codigo, $ini_m1, $fin_m1, 2);
                $ve_ext_m2 = $this->importeVentasDivision($row->codigo, $ini_m2, $fin_m2, 2);
                $ve_ext_m3 = $this->importeVentasDivision($row->codigo, $ini_m3, $fin_m3, 2);
                $ve_ext_m4 = $this->importeVentasDivision($row->codigo, $ini_m4, $fin_m4, 2);
                $ve_ext_m5 = $this->importeVentasDivision($row->codigo, $ini_m5, $fin_m5, 2);
                $ve_ext_m6 = $this->importeVentasDivision($row->codigo, $ini_m6, $fin_m6, 2);
                $ve_ext_m7 = $this->importeVentasDivision($row->codigo, $ini_m7, $fin_m7, 2);
                $ve_ext_m8 = $this->importeVentasDivision($row->codigo, $ini_m8, $fin_m8, 2);
                $ve_ext_m9 = $this->importeVentasDivision($row->codigo, $ini_m9, $fin_m9, 2);
                $ve_ext_m10 = $this->importeVentasDivision($row->codigo, $ini_m10, $fin_m10, 2);
                $ve_ext_m11 = $this->importeVentasDivision($row->codigo, $ini_m11, $fin_m11, 2);
                $ve_ext_m12 = $this->importeVentasDivision($row->codigo, $ini_m12, $fin_m12, 2);

                $dataExterna = array(
                    'ene' => $ve_ext_m1, 'feb' => $ve_ext_m2, 'mar' => $ve_ext_m3, 'abr' => $ve_ext_m4,
                    'may' => $ve_ext_m5, 'jun' => $ve_ext_m6, 'jul' => $ve_ext_m7, 'ago' => $ve_ext_m8,
                    'set' => $ve_ext_m9, 'oct' => $ve_ext_m10, 'nov' => $ve_ext_m11, 'dic' => $ve_ext_m12
                );

                $data[] = ['division' => $row->descripcion, 'id' => $row->id_centro_costo, 'interna' => $dataInterna, 'externa' => $dataExterna];
            }
        }

        LogActividad::registrar(Auth::user(), 'Reporte de Ventas - Módulo Gerencial', 9, null, null, null, 'Periodo: ' . $request->periodo);
        return response()->json(array('response' => 'ok', 'data' => $data), 200);
    }

    public function busquedaDashboard(Request $request)
    {
        $mes = ($request->mes > 0) ? $request->mes : date('n');
		$nom_mes = $this->hallarMes($mes);
        if ($request->periodo > 0) {
            $periodo = $request->periodo;
            $id_periodo = $request->id_periodo;
        } else {
            $periodo = date('Y');
            $peri = PeriodoModel::where('descripcion', $periodo)->orderBy('id_periodo', 'desc')->first();
            $id_periodo = $peri->id_periodo;
        }
        $ven_mes = 0;
        $met_mes = 0;

        $ini_m1 = $this->primerDia(1, $periodo);
        $fin_m1 = $this->ultimoDia(1, $periodo);
        $ini_m2 = $this->primerDia(2, $periodo);
        $fin_m2 = $this->ultimoDia(2, $periodo);
        $ini_m3 = $this->primerDia(3, $periodo);
        $fin_m3 = $this->ultimoDia(3, $periodo);
        $ini_m4 = $this->primerDia(4, $periodo);
        $fin_m4 = $this->ultimoDia(4, $periodo);
        $ini_m5 = $this->primerDia(5, $periodo);
        $fin_m5 = $this->ultimoDia(5, $periodo);
        $ini_m6 = $this->primerDia(6, $periodo);
        $fin_m6 = $this->ultimoDia(6, $periodo);
        $ini_m7 = $this->primerDia(7, $periodo);
        $fin_m7 = $this->ultimoDia(7, $periodo);
        $ini_m8 = $this->primerDia(8, $periodo);
        $fin_m8 = $this->ultimoDia(8, $periodo);
        $ini_m9 = $this->primerDia(9, $periodo);
        $fin_m9 = $this->ultimoDia(9, $periodo);
        $ini_m10 = $this->primerDia(10, $periodo);
        $fin_m10 = $this->ultimoDia(10, $periodo);
        $ini_m11 = $this->primerDia(11, $periodo);
        $fin_m11 = $this->ultimoDia(11, $periodo);
        $ini_m12 = $this->primerDia(12, $periodo);
        $fin_m12 = $this->ultimoDia(12, $periodo);

        $ve_m1 = $this->importeVentas($ini_m1, $fin_m1, 2);
        $ve_m2 = $this->importeVentas($ini_m2, $fin_m2, 2);
        $ve_m3 = $this->importeVentas($ini_m3, $fin_m3, 2);
        $ve_m4 = $this->importeVentas($ini_m4, $fin_m4, 2);
        $ve_m5 = $this->importeVentas($ini_m5, $fin_m5, 2);
        $ve_m6 = $this->importeVentas($ini_m6, $fin_m6, 2);
        $ve_m7 = $this->importeVentas($ini_m7, $fin_m7, 2);
        $ve_m8 = $this->importeVentas($ini_m8, $fin_m8, 2);
        $ve_m9 = $this->importeVentas($ini_m9, $fin_m9, 2);
        $ve_m10 = $this->importeVentas($ini_m10, $fin_m10, 2);
        $ve_m11 = $this->importeVentas($ini_m11, $fin_m11, 2);
        $ve_m12 = $this->importeVentas($ini_m12, $fin_m12, 2);

        $sum_ventas = $ve_m1 + $ve_m2 + $ve_m3 + $ve_m4 + $ve_m5 + $ve_m6 + $ve_m7 + $ve_m8 + $ve_m9 + $ve_m10 + $ve_m11 + $ve_m12;

        $query = IndicadorMensual::where('id_periodo', $id_periodo)->latest()->first();
        
        if ($query != null) {
            $m1 = (float) $query->ene;
            $m2 = (float) $query->feb;
            $m3 = (float) $query->mar;
            $m4 = (float) $query->abr;
            $m5 = (float) $query->may;
            $m6 = (float) $query->jun;
            $m7 = (float) $query->jul;
            $m8 = (float) $query->ago;
            $m9 = (float) $query->set;
            $m10 = (float) $query->oct;
            $m11 = (float) $query->nov;
            $m12 = (float) $query->dic;
        } else {
            $m1 = 0;
            $m2 = 0;
            $m3 = 0;
            $m4 = 0;
            $m5 = 0;
            $m6 = 0;
            $m7 = 0;
            $m8 = 0;
            $m9 = 0;
            $m10 = 0;
            $m11 = 0;
            $m12 = 0;
        }
        $sum_meta = $m1 + $m2 + $m3 + $m4 + $m5 + $m6 + $m7 + $m8 + $m9 + $m10 + $m11 + $m12;

        switch ($mes) {
            case '1':
                $ven_mes = $ve_m1;
                $met_mes = $m1;
            break;
            case '2':
                $ven_mes = $ve_m2;
                $met_mes = $m2;
            break;
            case '3':
                $ven_mes = $ve_m3;
                $met_mes = $m3;
            break;
            case '4':
                $ven_mes = $ve_m4;
                $met_mes = $m4;
            break;
            case '5':
                $ven_mes = $ve_m5;
                $met_mes = $m5;
            break;
            case '6':
                $ven_mes = $ve_m6;
                $met_mes = $m6;
            break;
            case '7':
                $ven_mes = $ve_m7;
                $met_mes = $m7;
            break;
            case '8':
                $ven_mes = $ve_m8;
                $met_mes = $m8;
            break;
            case '9':
                $ven_mes = $ve_m9;
                $met_mes = $m9;
            break;
            case '10':
                $ven_mes = $ve_m10;
                $met_mes = $m10;
            break;
            case '11':
                $ven_mes = $ve_m11;
                $met_mes = $m11;
            break;
            case '12':
                $ven_mes = $ve_m12;
                $met_mes = $m12;
            break;
        }

        return response()->json(array('total_venta' => $sum_ventas, 'total_meta' => $sum_meta, 'meta_mes' => $met_mes, 'ventas_mes' => $ven_mes, 'mes' => $mes, 'periodo' => $periodo, 'nombre_mes' => $nom_mes), 200);
    }

    public function importeVentasEmpresa($empresa, $ini, $fin, $venta, $tipo = 1)
    {
        if ($venta == 1) {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                            ->whereBetween('fecha', [$ini, $fin])->where([['id_empresa', $empresa], ['estado', 1], ['nombre_vendedor', 28]])
                            ->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                            ->whereBetween('fecha', [$ini, $fin])->where([['id_empresa', $empresa], ['estado', 1], ['nombre_vendedor', 28], ['id_tipo_documento', 3]])->get();
        } else {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                            ->whereBetween('fecha', [$ini, $fin])->where([['id_empresa', $empresa], ['estado', 1], ['nombre_vendedor', '!=', 28]])
                            ->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                            ->whereBetween('fecha', [$ini, $fin])->where([['id_empresa', $empresa], ['estado', 1], ['nombre_vendedor', '!=', 28], ['id_tipo_documento', 3]])->get();
        }

        $tot_ve = ($total_ve[0]['total'] != null) ? $total_ve[0]['total'] : 0;
        $tot_nc = ($total_nc[0]['total'] != null) ? $total_nc[0]['total'] : 0;
        $total = ($tot_ve / 1.18) - ($tot_nc / 1.18);

		if ($tipo == 1) {
            return $total;
		} else {
            $array = array(
                'ventas'    => number_format($tot_ve, 5),
                'ncredito'  => number_format($tot_nc, 5),
                'total'     => number_format($total, 5)
            );
			return response()->json($array, 200);
		}
    }

    public function importeVentas($ini, $fin, $venta, $tipo = 1)
    {
        if ($venta == 1) {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['estado', 1], ['nombre_vendedor', 28]])->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['estado', 1], ['nombre_vendedor', 28], ['id_tipo_documento', 3]])->get();
        } else {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['estado', 1], ['nombre_vendedor', '!=', 28]])->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['estado', 1], ['nombre_vendedor', '!=', 28], ['id_tipo_documento', 3]])->get();
        }

        $tot_ve = ($total_ve[0]['total'] != null) ? $total_ve[0]['total'] : 0;
        $tot_nc = ($total_nc[0]['total'] != null) ? $total_nc[0]['total'] : 0;
        $total = ($tot_ve / 1.18) - ($tot_nc / 1.18);

		if ($tipo == 1) {
            return $total;
		} else {
            $array = array(
                'ventas'    => number_format($tot_ve, 5),
                'ncredito'  => number_format($tot_nc, 5),
                'total'     => number_format($total, 5)
            );
			return response()->json($array, 200);
		}
    }

    public function importeVentasDivision($codigo, $ini, $fin, $venta, $tipo = 1)
    {
        if ($venta == 1) {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['codigo_centro_costo', 'like', $codigo.'%'], ['estado', 1], ['nombre_vendedor', 28]])
                        ->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['codigo_centro_costo', 'like', $codigo.'%'], ['estado', 1], ['nombre_vendedor', 28], ['id_tipo_documento', 3]])->get();
        } else {
            $total_ve = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['codigo_centro_costo', 'like', $codigo.'%'], ['estado', 1], ['nombre_vendedor', '!=', 28]])
                        ->whereIn('id_tipo_documento', [1, 2])->get();
    
            $total_nc = VentaModel::select(DB::raw('SUM(importe * tc) AS total'))
                        ->whereBetween('fecha', [$ini, $fin])->where([['codigo_centro_costo', 'like', $codigo.'%'], ['estado', 1], ['nombre_vendedor', '!=', 28], ['id_tipo_documento', 3]])->get();
        }

        $tot_ve = ($total_ve[0]['total'] != null) ? $total_ve[0]['total'] : 0;
        $tot_nc = ($total_nc[0]['total'] != null) ? $total_nc[0]['total'] : 0;
        $total = ($tot_ve / 1.18) - ($tot_nc / 1.18);

		if ($tipo == 1) {
            return $total;
		} else {
            $array = array(
                'ventas'    => number_format($tot_ve, 5),
                'ncredito'  => number_format($tot_nc, 5),
                'total'     => number_format($total, 5)
            );
			return response()->json($array, 200);
		}
    }

    public function ultimoDia($mes, $anio)
    { 
        $month = $mes;
        $year = $anio;
        $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
    }

    public function primerDia($mes, $anio)
    {
        $month = $mes;
        $year = $anio;
        return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
    }
	
	function hallarMes($val)
    {
        $meses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
        $fin = $meses[$val];
        return $fin;
    }

    public function enviarData($data, $url)
    {
        $cUrl= curl_init();
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($cUrl, CURLOPT_VERBOSE, true);
        curl_setopt($cUrl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cUrl, CURLOPT_URL, $url);
        curl_setopt($cUrl, CURLOPT_HEADER, 0);
        curl_setopt($cUrl, CURLOPT_POST, 1);
        curl_setopt($cUrl, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($cUrl);
    }
}
