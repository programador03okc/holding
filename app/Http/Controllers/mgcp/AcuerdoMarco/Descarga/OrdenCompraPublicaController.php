<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Descarga;

use App\Helpers\mgcp\EntidadHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;

use App\Helpers\mgcp\OrdenCompraPublicaHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\OrdenCompra\Publica\DescargaOcPublicaFallida;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use stdClass;


class OrdenCompraPublicaController extends Controller
{
    private $nombreFormulario = 'Descargar detalles de O/C públicas';

    public function index()
    {
        if (!Auth::user()->tieneRol(53)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $acuerdos = AcuerdoMarco::orderBy('descripcion', 'desc')->get();
        return view('mgcp.acuerdo-marco.descarga.oc-publicas.detalles', get_defined_vars());
    }

    public function obtenerDetalles(Request $request)
    {
        $portal = new PeruComprasHelper();
        $acuerdo = AcuerdoMarco::find($request->acuerdo);
        $desde = Carbon::createFromFormat('d-m-Y', $request->desde);
        $hasta = Carbon::createFromFormat('d-m-Y', $request->hasta);
        $respuesta = $portal->enviarData($acuerdo->id_pc . '^^^^' . $desde->toDateString() . '^' . $hasta->toDateString() . '^BIENES', 'https://www.catalogos.perucompras.gob.pe/ConsultaOrdenesPub/consultaOrdenes');
        $lista = explode("¬", substr($respuesta, strpos($respuesta, '¯') + 2));
        $resultado = '';
        $contador = 1;
        foreach ($lista as $fila) {
            $item = explode("^", $fila);
            $resultado .= "<tr>
            <td class='text-center'>$contador</td>
            <td class='id'>" . substr($item[0], 0, strpos($item[0], '-')) . "</td>
            <td class='ruc-proveedor'>$item[1]</td>
            <td class='proveedor'>$item[2]</td>
            <td class='ruc-entidad'>$item[3]</td>
            <td class='entidad'>$item[4]</td>
            <td class='orden'>$item[6]</td>
            <td class='fecha'>$item[9]</td>
            <td class='monto'>$item[13]</td>
            <td class='estado'></td>
            </tr>";
            $contador++;
        }
        return response()->json(array('tipo' => 'success', 'resultado' => $resultado), 200);
    }

    /*public function test()
    {
        set_time_limit(240);
        $filas=DB::select('SELECT * FROM mgcp_acuerdo_marco.oc_revisar WHERE "Monto">=10000 AND "Id" NOT IN (
            SELECT mgcp_acuerdo_marco.oc_publicas.id FROM mgcp_acuerdo_marco.oc_publicas WHERE EXTRACT(YEAR FROM fecha_formalizacion)=2019)
            AND "Id" NOT IN (SELECT id_oc FROM mgcp_acuerdo_marco.descarga_oc_publica_fallidas) ORDER BY "Id"');
        foreach ($filas as $fila)
        {
            //die($fila->{"RUC entidad"}.' '.$fila->{"Entidad"});
            $idEntidad=EntidadHelper::obtenerIdPorRuc($fila->{"RUC entidad"},$fila->{"Entidad"},null);
            //echo "id es ".$idEntidad;
            $helper = new OrdenCompraPublicaHelper($fila->Id, $idEntidad);
            echo "ID $fila->Id:".$helper->procesar().'<br>';
        }
        
        if ($helper->procesar()) {
            return response()->json(array('mensaje' => 'Registrada', 'tipo' => 'success'), 200);
        } else {
            return response()->json(array('mensaje' => 'Ya registrada', 'tipo' => 'warning'), 200);
        }
    }*/

    public function procesar(Request $request)
    {
        if (($request->acuerdo == 11 && $request->monto < 2000) || ($request->acuerdo != 11 && $request->monto < 3000)) { //Era 10000
            return response()->json(array('mensaje' => "O/C ignorada por monto", 'tipo' => 'warning'), 200);
        }

        $entidad = Entidad::where('nombre', $request->entidad)->first() ?? new Entidad();
        $entidad->ruc = $request->ruc_entidad;
        $entidad->nombre = $request->entidad;
        $entidad->save();

        $helper = new OrdenCompraPublicaHelper($request->id, $entidad->id);
        switch ($helper->procesar()) {
            case 0:
                return response()->json(array('mensaje' => 'Registrada', 'tipo' => 'success'), 200);
                break;
            case 1:
                return response()->json(array('mensaje' => 'Ya registrada', 'tipo' => 'warning'), 200);
                break;
            case 2:
                return response()->json(array('mensaje' => "O/C ignorada por error previo", 'tipo' => 'warning'), 200);
                break;
            default:
                return response()->json(array('mensaje' => 'Problema al registrar', 'tipo' => 'danger'), 200);
                break;
        }




        /*$helper = new OrdenCompraPublicaHelper($request->id);
        $resultadoConversion = $helper->leerArchivoParaDetalles();

        if ($resultadoConversion != 0) {
            switch ($resultadoConversion) {
                case 1:
                    $mensaje = 'No se pudo descargar el archivo PDF del portal';
                    break;
                case 2:
                    $mensaje = 'No se pudo guardar el archivo convertido para procesar';
                    break;
                default:
                    $mensaje = 'No se pudo convertir el archivo descargado';
                    break;
            }
            return response()->json(array('mensaje' => $mensaje, 'tipo' => 'danger'), 200);
        }

        $helper->leerProvincia();

        try {
            DB::beginTransaction();
            $helper->orden->orden_compra = $request->orden;
            $helper->orden->ruc_proveedor = $request->ruc_proveedor;
            $helper->orden->razon_social = $request->proveedor;
            $helper->orden->fecha_formalizacion = Carbon::createFromFormat('d/m/Y H:i:s', $request->fecha)->toDateString();
            $helper->leerEntidad($request->ruc_entidad, $request->entidad);
            //$helper->leerProvincia();
            $helper->orden->save();
            $helper->leerDetalles();
            DB::commit();
            return response()->json(array('mensaje' => 'Registrada', 'tipo' => 'success'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('mensaje' => 'Hubo un problema al registrar: ' . $e->getMessage(), 'tipo' => 'danger'), 200);
        }*/
    }


    /*public function productos()
    {
        if (!Auth::user()->tieneRol(53)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $pendientes = OrdenCompraPublicaDetalle::whereNull('id_producto')->distinct()->get(['id_orden_compra']);
        if ($pendientes->count() == 0) {
            return view('mgcp.acuerdo-marco.descarga.oc-publicas.fin_productos');
        }
        //$filas = OrdenCompraPublicaDetalle::where('id_orden_compra', $pendiente->id_orden_compra)->whereNull('id_producto')->where('revisado', false)->orderBy('id', 'asc')->get();
        return view('mgcp.acuerdo-marco.descarga.oc-publicas.productos')->with(compact('pendientes'));
    }

    public function procesarProducto(Request $request)
    {
        if (!Auth::user()->tieneRol(53)) {
            return view('mgcp.usuario.sin_permiso');
        }
        try {
            $helper = new OrdenCompraPublicaHelper($request->idOc);
            $helper->leerProductos();
            return response()->json(array('mensaje' => 'Procesado', 'tipo' => 'success'), 200);
        } catch (\Exception $ex) {
            return response()->json(array('mensaje' => 'Problema: ' . $ex->getMessage(), 'tipo' => 'danger'), 200);
        }
    }*/
}
