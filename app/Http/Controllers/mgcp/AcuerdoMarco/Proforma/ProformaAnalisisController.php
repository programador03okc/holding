<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Proforma;

use App\Exports\FormatoProformaExport;
use App\Helpers\mgcp\AcuerdoMarco\Proforma\ProformaFiltrosHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProducto;
use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProductoDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\ComentarioCompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\AcuerdoMarco\Proforma\ProformaAnalisis;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProformaAnalisisController extends Controller
{
    public function listar(Request $request)
    {
        $data = [];
        try {
            $query = ProformaAnalisis::with('producto')->where('id_proforma', $request->idProforma);
            if ($query->count() > 0) {
                $data = $query->get();
                $response = 'ok';
                $alert = 'info';
                $message = 'Se encontró datos de análisis registrados anteriormente';
            } else {
                $response = 'null';
                $alert = 'warning';
                $message = 'No se encontraron datos de análisis registrados';
            }
            $exception = '';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo';
            $exception = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'datos' => $data, 'exception' => $exception), 200);
    }

    public function registrar(Request $request)
    {
        try {
            $data = ProformaAnalisis::firstOrNew(['id' => $request->id_proforma_analisis]);
                $data->id_proforma = $request->id_proforma;
                $data->codigo_proforma = $request->codigo_proforma;
                $data->id_proveedor = $request->id_proveedor;
                $data->id_producto = $request->id_producto_ext;
                $data->cantidad = $request->cantidad;
                $data->tipo_cambio = $request->tcSbs;
                $data->precio_costo = str_replace(',', '', $request->costo_ext);
                $data->precio_soles = str_replace(',', '', $request->precio_sol_ext);
                $data->precio_dolares = str_replace(',', '', $request->precio_dol_ext);
                $data->total = str_replace(',', '', $request->total_ext);
                $data->margen = str_replace('%', '', $request->margen_ext);
                $data->tipo_proforma = $request->id_tipo_proforma;
            $data->save();

            $response = 'ok';
            $alert = 'success';
            if ($request->id_proforma_analisis > 0) {
                $message = 'Se ha editado el análisis de proforma con éxito';
            } else {
                $message = 'Se ha registrado el análisis de proforma con éxito';
            }
            $exception = '';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo';
            $exception = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'exception' => $exception), 200);
    }

    public function actualizarFiltros(Request $request)
    {
        ProformaFiltrosHelper::actualizar($request);
        return response()->json(array('data' => 'success'), 200);
    }

    public function descargarFormato($tipoProforma)
    {
        $data = $this->filtroAnalisisProformas($tipoProforma, 1);
        $archivo = ($tipoProforma == 1) ? 'proforma_compra_ordinaria.xlsx' : 'proforma_gran_compra.xlsx';
        return Excel::download(new FormatoProformaExport($data, $tipoProforma), $archivo);
    }

    public function actualizarProbabilidad(Request $request)
    {
        if ($request->tipoProforma == 1) {
            CompraOrdinaria::where('proforma', $request->proforma)->where('id_producto', $request->producto)->update(['probabilidad_ganar' => false]);
            $data = CompraOrdinaria::find($request->idProforma);
        } else {
            GranCompra::where('proforma', $request->proforma)->where('id_producto', $request->producto)->update(['probabilidad_ganar' => false]);
            $data = GranCompra::find($request->idProforma);
        }
            $data->probabilidad_ganar = $request->valor;
        $data->save();

        if ($request->valor == 'false') {
            $profAnterior = ProformaAnalisis::where('id_proforma', $request->idProforma);
            if ($profAnterior->count() > 0) {
                $profAnterior->delete();
            }
        }
        
        $response = '';
        $alert = '';
        $message = '';

        if ($data) {
            if ($request->valor == 'true') {
                $alert = 'success';
                $message = 'Opción seleccionada como MPG';
            } else {
                $alert = 'info';
                $message = 'Se desactivo la opción como MPG';
            }
            $response = 'ok';
        } else {
            $alert = 'null';
            $response = 'warning';
            $message = 'Opción seleccionada como MPG';
        }

        return response()->json(array('response' => $response, 'tipo' => $alert, 'mensaje' => $message), 200);
    }

    public function replicarProformas()
    {
        $proformas = ProformaAnalisis::all();
        foreach ($proformas as $key) {
            if ($key->tipo_proforma == 1) {
                $datos = CompraOrdinaria::find($key->id_proforma);
            } else {
                $datos = GranCompra::find($key->id_proforma);
            }

            $prof = ProformaAnalisis::find($key->id);
                $prof->codigo_proforma = $datos->proforma;
            $prof->save();
        }
        die('***FIN DEL PROCESO***');
    }

    public function testProforma()
    {
        $empresas = session('proformaEmpresas');
        $femDesde = Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionDesde'))->toDateString();
        $femHasta = Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionHasta'))->toDateString();
        $fliDesde = Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteDesde'))->toDateString();
        $fliHasta = Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteHasta'))->toDateString();
        $estados = session('proformaEstado');
        $tp_carga = session('proformaTipoCarga');
        $may_prob = session('proformaMPG');
        $catalogo = session('proformaCatalogos');
        $marcas = session('proformaMarcas');
        $departam = session('proformaDepartamentos');

        $array = array(
            'empresas'   => $empresas,
            'femDesde'   => $femDesde,
            'femHasta'   => $femHasta,
            'fliDesde'   => $fliDesde,
            'fliHasta'   => $fliHasta,
            'estados'    => $estados,
            'tp_carga'   => $tp_carga,
            'may_prob'   => $may_prob,
            'catalogo'   => $catalogo,
            'marcas'     => $marcas,
            'departam'   => $departam
        );

        $proformas = $this->filtroAnalisisProformas(1, 1);
        return response()->json(array('proformas' => $proformas, 'sesiones' => $array), 200);
        // dd(session()->has('proformaMPG'));
        exit();

        $query = CompraOrdinaria::select(['nro_proforma', 'proforma', 'fecha_emision', 'fecha_limite', 'inicio_entrega', 'fin_entrega', 'marca', 'modelo', 
                'part_no', 'id_entidad', 'id_empresa', 'id_producto', 'software_educativo', 'cantidad', 'precio_unitario_base', 'moneda_ofertada', 'id_ultimo_usuario',
                'estado', 'costo_envio_publicar', 'proforma', 'empresas.empresa AS nombre_empresa', 'entidades.nombre AS nombre_entidad', 'categorias.descripcion AS categoria', 
                'requiere_flete', 'precio_publicar', 'plazo_publicar','users.nombre_corto AS nombre_usuario'
            ])
        ->join('mgcp_acuerdo_marco.empresas', 'proformas_compra_ordinaria.id_empresa', '=', 'empresas.id')
        ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
        ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', 'id_categoria')
        ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', 'id_catalogo')
        ->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', 'id_departamento')
        ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', 'id_entidad')
        ->leftJoin('mgcp_usuarios.users', 'id_ultimo_usuario', 'users.id')
        ->whereIn('nro_proforma', $proformas)->get();

        $data = [];
        foreach ($query as $key) {
            $costo = CalculadoraProductoDetalle::where('nro_proforma', $key->nro_proforma)->where('tipo_proforma', 1)->sum('monto');
            $flete = CalculadoraProducto::where('nro_proforma', $key->nro_proforma)->where('tipo_proforma', 1)->sum('flete');
            $precio = (($key->precio_publicar + $flete) > 0) ? ($key->precio_publicar + $flete) : 0;
            $total = $precio * $key->cantidad;
                
            $proveedor = '';
            $marca = '';
            $part_no = '';
            $modelo = '';
            $costo_com = 0;
            $precio_com = 0;
            $margen_com = 0;
            $dataAnalisis = ProformaAnalisis::where('id_proforma', $key->nro_proforma);

            if ($dataAnalisis->count() > 0) {
                $proveedor = $dataAnalisis->first()->proveedor->nombre;
                $marca = $dataAnalisis->first()->producto->marca;
                $part_no = $dataAnalisis->first()->producto->part_no;
                $modelo = $dataAnalisis->first()->producto->modelo;
                $costo_com = $dataAnalisis->first()->precio_costo;
                $precio_com = $dataAnalisis->first()->precio_dolares;
                $margen_com = $dataAnalisis->first()->margen;
            }

            $comentarios = ComentarioCompraOrdinaria::where('id_proforma', $key->nro_proforma)->get();

            $data[] = [
                'nombre_entidad'        => $key->nombre_entidad,
                'marca'                 => $key->marca,
                'modelo'                => $key->modelo,
                'part_no'               => $key->part_no,
                'proforma'              => $key->proforma,
                'nro_proforma'          => $key->nro_proforma,
                'cantidad'              => $key->cantidad,
                'fin_entrega'           => $key->fin_entrega,
                'costo'                 => $costo,
                'precio_unitario_base'  => $key->precio_publicar,
                'precio_flete'          => $precio,
                'total'                 => $total,
                'margen'                => 0,
                'resultado'             => $key->estado,
                'comp_proveedor'        => $proveedor,
                'comp_marca'            => $marca,
                'comp_part_no'          => $part_no,
                'comp_modelo'           => $modelo,
                'comp_costo'            => $costo_com,
                'comp_precio'           => $precio_com,
                'comp_margen'           => $margen_com,
                'comentarios'           => $comentarios
            ];
        }
        return response()->json($data, 200);
    }

    public function filtroAnalisisProformas($tipoProforma, $return)
    {
        $valores = [];
        if ($tipoProforma == 1) {
            $query = CompraOrdinaria::with(['producto', 'entidad', 'empresa'])
            ->join('mgcp_acuerdo_marco.empresas', 'proformas_compra_ordinaria.id_empresa', '=', 'empresas.id')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'id_entidad')
            ->join('mgcp_acuerdo_marco.categorias', 'productos_am.id_categoria', '=', 'categorias.id')
            ->join('mgcp_acuerdo_marco.catalogos', 'id_catalogo', '=', 'catalogos.id')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', '=', 'acuerdo_marco.id')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_compra_ordinaria.id_ultimo_usuario')
            ->select(['proformas_compra_ordinaria.nro_proforma', 'proformas_compra_ordinaria.id_empresa', 'proforma', 'fecha_emision', 'moneda_ofertada', 'cantidad', 'entidades.nombre AS nombre_entidad',
                'lugar_entrega', 'id_catalogo', 'precio_publicar', 'costo_envio_publicar', 'estado', 'users.nombre_corto', 'users.id AS id_usuario', 'users.name', 'fecha_limite', 'empresas.empresa AS nombre_empresa',
                'software_educativo', 'entidades.ruc', 'requerimiento', 'precio_unitario_base', 'plazo_publicar', 'probabilidad_ganar', 'productos_am.marca', 'productos_am.modelo', 'categorias.descripcion as categoria',
                'productos_am.part_no', 'productos_am.descripcion AS descripcion_producto', 'requiere_flete', 'inicio_entrega', 'fin_entrega', 'id_producto', 'id_entidad', 'tipo_cambio']);
        } else {
            $query = GranCompra::with(['producto','entidad','empresa'])
            ->join('mgcp_acuerdo_marco.empresas', 'proformas_gran_compra.id_empresa', '=', 'empresas.id')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'id_entidad')
            ->join('mgcp_acuerdo_marco.categorias', 'productos_am.id_categoria', '=', 'categorias.id')
            ->join('mgcp_acuerdo_marco.catalogos', 'id_catalogo', '=', 'catalogos.id')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', '=', 'acuerdo_marco.id')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_gran_compra.id_ultimo_usuario')
            ->select(['proformas_gran_compra.nro_proforma', 'proformas_gran_compra.id_empresa', 'proforma', 'fecha_emision', 'moneda_ofertada', 'cantidad', 'entidades.nombre AS nombre_entidad',
                'lugar_entrega', 'id_catalogo', 'precio_publicar', 'costo_envio_publicar', 'estado', 'users.nombre_corto', 'users.id AS id_usuario','users.name','fecha_limite', 'empresas.empresa AS nombre_empresa',
                'software_educativo', 'entidades.ruc','requerimiento', 'precio_unitario_base', 'plazo_publicar', 'probabilidad_ganar', 'productos_am.marca', 'productos_am.modelo', 'categorias.descripcion as categoria',
                'productos_am.part_no', 'productos_am.descripcion AS descripcion_producto', 'requiere_flete', 'inicio_entrega', 'fin_entrega', 'id_producto','id_entidad', 'tipo_cambio']);
        }

        if (session()->has('proformaEmpresas')) {
            if ($tipoProforma == 1) {
                $query = $query->whereIn('proformas_compra_ordinaria.id_empresa', session('proformaEmpresas'));
            } else {
                $query = $query->whereIn('proformas_gran_compra.id_empresa', session('proformaEmpresas'));
            }
        }
        if (session()->has('proformaFechaEmisionDesde')) {
            $query = $query->whereBetween('fecha_emision', [Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionDesde'))->toDateString(), Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionHasta'))->toDateString()]);
        }
        if (session()->has('proformaFechaLimiteDesde')) {
            $query = $query->whereBetween('fecha_limite', [Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteDesde'))->toDateString(), Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteHasta'))->toDateString()]);
        }
        if (session()->has('proformaEstado')) {
            $valores['estado'] = session('proformaEstado');
        }
        if ($tipoProforma == 1) {
            if (session()->has('proformaTipoCarga')) {
                $valores['tipo_carga'] = session('proformaTipoCarga');
            }
        }
        if (count($valores) > 0) {
            $query = $query->where($valores);
        }
        if (session()->has('proformaMPG')) {
            $query = $query->where('probabilidad_ganar', session('proformaMPG'));
        }
        if (session()->has('proformaCatalogos')) {
            $query = $query->whereIn('id_catalogo', session('proformaCatalogos'));
        }
        if (session()->has('proformaMarcas')) {
            $query = $query->whereIn('marca', session('proformaMarcas'));
        }
        if (session()->has('proformaDepartamentos')) {
            $query = $query->whereIn('id_departamento', session('proformaDepartamentos'));
        }
        $query = $query->orderBy('id_producto', 'asc')->get();

        $auxiliar = 9999999;
        $costo = 0;
        $arrayProformas = [];
        $arrayMPG = [];
        $arrayProductos = [];
        $proformas = [];
        $nroProforma = '';
        $data = [];

        foreach ($query as $key) {
            $flete = CalculadoraProducto::where('nro_proforma', $key->nro_proforma)->where('tipo_proforma', 1)->sum('flete');
            $nuevoPrecio = $key->precio_publicar + $flete;

            if ($key->probabilidad_ganar == true) {
                $costo = $nuevoPrecio;
                $nroProforma = $key->nro_proforma;
                array_push($arrayMPG, $key->proforma);
            } else {
                if (!in_array($key->proforma, $arrayMPG)) {
                    if (in_array($key->proforma, $arrayProformas)) { // buscar si existe en el array
                        if (in_array($key->id_producto, $arrayProductos)) { // buscar si existe el producto
                            if ($nuevoPrecio <= $auxiliar) {
                                $costo = $nuevoPrecio;
                                $nroProforma = $key->nro_proforma;
                            } else {
                                $costo = $auxiliar;
                            }
                        } else { // no se encontró producto -> nuevo
                            $auxiliar = 9999999; // reiniciar el valor del costo auxiliar
                            $nroProforma = ''; // reiniciar el nro de la proforma
                            if ($nuevoPrecio <= $auxiliar) {
                                $costo = $nuevoPrecio;
                            }
                            $nroProforma = $key->nro_proforma;
                            $auxiliar = $costo;
                        }
                    } else { // no se encontró proforma -> nueva
                        $auxiliar = 9999999; // reiniciar el valor del costo auxiliar
                        $nroProforma = ''; // reiniciar el nro de la proforma
                        if ($nuevoPrecio <= $auxiliar) {
                            $costo = $nuevoPrecio;
                            $nroProforma = $key->nro_proforma;
                        }
                        $auxiliar = $costo;
                    }
                    array_push($arrayProductos, $key->id_producto);
                    array_push($arrayProformas, $key->proforma);
                }
            }

            $proformas[$key->proforma.'-'.$key->id_producto] = ['costo' => $costo, 'nro_proforma' => $nroProforma];
        }

        foreach ($proformas as $row => $value) {
            array_push($data, $value['nro_proforma']);
        }

        if ($return == 1) {
            return $data;
        } else {
            return response()->json($data, 200);
        }
    }

    public function validarProforma()
    {
        $query = ProformaAnalisis::whereIn('id', [51, 52])->get();
        $array = [];
        foreach ($query as $key) {
            $proforma = CompraOrdinaria::find($key->id_proforma);
                $proforma->probabilidad_ganar = true;
            $proforma->save();
            array_push($array, $proforma->nro_proforma);
        }

        return response()->json($array, 200);
    }
}
