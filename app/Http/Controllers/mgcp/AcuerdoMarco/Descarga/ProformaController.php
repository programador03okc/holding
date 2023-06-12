<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Descarga;

use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualRegistroHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPaqueteRegistroHelper;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaPortalHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Proforma\DescargaProforma;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\EmpresaAcuerdo;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProformaController extends Controller
{
    private $nombreFormulario = 'Descargar proformas';

    public function index()
    {
        if (!Auth::user()->tieneRol(41)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.descarga.proformas.index', get_defined_vars());
    }

    public function listaUltimasDescargas()
    {
        if (!Auth::user()->tieneRol(41)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $descargas = DB::select("SELECT empresas.empresa, users.\"name\" AS usuario, TO_CHAR(fecha_fin,'DD-MM-YYYY HH:MI PM') AS fecha_fin FROM (
            SELECT id, id_empresa, id_usuario, fecha_fin, rank() OVER (PARTITION BY id_empresa ORDER BY fecha_fin DESC)
            FROM mgcp_acuerdo_marco.descargas_proformas) AS resultado
            INNER JOIN mgcp_acuerdo_marco.empresas ON empresas.id = id_empresa
            INNER JOIN mgcp_usuarios.users ON users.id = id_usuario
            WHERE empresas.id = (?) AND rank = 1", [Auth::user()->id_empresa]);
        return view('mgcp.acuerdo-marco.descarga.proformas.lista_ultimas_descargas')->with(compact('descargas'));
    }

    public function obtenerFechasUltimaDescarga()
    {
        $lista = DB::select("SELECT empresas.empresa, users.\"name\" AS usuario, fecha_fin FROM (
            SELECT id, id_empresa, id_usuario, fecha_fin, rank() OVER (PARTITION BY id_empresa ORDER BY fecha_fin DESC)
            FROM mgcp_acuerdo_marco.descargas_proformas) AS resultado
            INNER JOIN mgcp_acuerdo_marco.empresas ON empresas.id = id_empresa
            INNER JOIN mgcp_usuarios.users ON users.id = id_usuario
            WHERE empresas.id = (?) AND rank = 1 ORDER BY empresas.id", [Auth::user()->id_empresa]);
        $data = "";
        foreach ($lista as $fila) {
            $data .= '<tr>';
            $data .= '<td class="text-center">' . $fila->empresa . '</td>';
            $data .= '<td>' . $fila->usuario . '</td>';
            $fecha = new Carbon($fila->fecha_fin);
            $data .= '<td class="text-center">' . $fecha->format('d-m-Y g:i A') . '<p class="help-block">(' . $fecha->diffForHumans() . ')</p></td>';
            $data .= '</tr>';
        }
        return $data;
    }

    public function registrarDescarga(Request $request)
    {
        DescargaProforma::registrar($request->idEmpresa, $request->idAcuerdo, $request->idCatalogo, $request->dias,  Auth::user()->id);
        return response()->json(array('mensaje' => 'Ok'), 200);
    }

    public function obtenerProformas(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3); // ealvarez 2
        $filas = ProformaPortalHelper::obtenerListado($portal, $request->idAcuerdo, $request->idCatalogo, $request->tipoProforma, $request->tipoContratacion, $request->diasAntiguedad);
        $datos = '';
        $contador = 1;
        foreach ($filas->pLista as $fila) {
            $datos .= '<tr>';
            $datos .= '<td class="text-center">'. $contador .'</td>';
            $datos .= '<td class="nroProforma">'. $fila->N_Proforma .'</td>';
            $datos .= '<td class="nroRequerimiento">'. $fila->N_Requerimento .'</td>';
            $datos .= '<td class="proforma">'. $fila->C_Proforma .'</td>';
            $datos .= '<td class="requerimiento">'. $fila->C_Requerimento .'</td>';
            $datos .= '<td><span class="ruc">'. $fila->C_Ruc .'</span> <span class="entidad">'. $fila->C_Entidad .'</span> <span class="semaforo">'. $fila->N_EntidadIndicadorSemaforo .'</span></td>';
            $datos .= '<td class="fechaEmision">'. $fila->C_FechaEmision .'</td>';
            $datos .= '<td class="fechaLimite">'. $fila->C_FechLimCoti .'</td>';
            $datos .= '<td class="fichaProducto">'. $fila->C_Ficha .'</td>';
            $datos .= '<td class="estado">'. $fila->C_Estado .'</td>';
            $datos .= '<td class="resultado text-center"></td>';
            $datos .= '</tr>';
            $contador++;
        }
        return response()->json(array('datos' => $datos, 'filas' => $contador), 200);
    }

    public function procesarProforma(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $acuerdo = AcuerdoMarco::where('id_pc', $request->idAcuerdo)->first();

        if ($request->tipoContratacion == 0) {
            return response()->json(ProformaIndividualRegistroHelper::registrar($request->tipoProforma, $empresa, $acuerdo, $request), 200);
        } else {
            return response()->json(ProformaPaqueteRegistroHelper::registrar($request->tipoProforma, $empresa, $acuerdo, $request), 200);
        }
    }

    public function descargaAutomatica($idEmpresa, $diasAntiguedad)
    {
        set_time_limit(6000);
        $tiposProforma = ['GRANCOMPRA', 'NORMAL'];
        $tiposContratacion = [0, 1]; //0: Individual, 1: Paquete
        $empresa = Empresa::find($idEmpresa);
        $tpUsuario = 3;

        foreach ($tiposContratacion as $tipoContratacion) {
            foreach ($tiposProforma as $tipoProforma) {
                $empresaAcuerdos = EmpresaAcuerdo::join('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', '=', 'id_acuerdo_marco')
                                    ->where('id_empresa', $empresa->id)->where('activo', true)->orderBy('acuerdo_marco.id', 'asc')->get();

                foreach ($empresaAcuerdos as $empresaAcuerdo) {
                    $acuerdo = AcuerdoMarco::find($empresaAcuerdo->id_acuerdo_marco);
                    $catalogos = Catalogo::where('id_acuerdo_marco', $acuerdo->id)->get();

                    foreach ($catalogos as $catalogo) {
                        $portal = new PeruComprasHelper();

                        $reintentar = true;
                        while ($reintentar) {
                            $proformas = ProformaPortalHelper::obtenerListado($portal, $acuerdo->id_pc, $catalogo->id_pc, $tipoProforma, $tipoContratacion, $diasAntiguedad);
                            if ($proformas == null) {
                                if ($idEmpresa == 1) {
                                    $tpUsuario = 2;
                                }
                                $portal->login($empresa, $tpUsuario); // ealvarez 2
                            } else {
                                $reintentar = false;
                            }
                        }
                        
                        foreach ($proformas->pLista as $fila) {
                            if ($tipoContratacion == 0) {
                                ProformaIndividualRegistroHelper::registrar($tipoProforma, $empresa, $acuerdo, $fila);
                            } else {
                                ProformaPaqueteRegistroHelper::registrar($tipoProforma, $empresa, $acuerdo, $fila);
                            }
                        }
                    }
                }
            }
        }
        DescargaProforma::registrar($empresa->id, null, null, $diasAntiguedad, 44); //44 - Usuario Descarga de proformas
    }
}
