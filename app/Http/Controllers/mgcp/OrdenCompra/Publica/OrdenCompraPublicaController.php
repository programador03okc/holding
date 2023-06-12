<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Publica;

use App\Exports\AnalisisOrdenCompraExport;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaDetalle;
/* use App\Http\Controllers\Acuerdomarco\AccesoPeruCompras; */
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Helpers\mgcp\WebHelper;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Proforma\ProformaAnalisis;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaAnalisis;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaProveedor;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class OrdenCompraPublicaController extends Controller
{
    private $nombreFormulario = 'Órdenes de compra públicas';
    const TOTAL_FILAS_VISTA = 20;

    public function lista()
    {
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $catalogos = Catalogo::orderBy('id', 'asc')->get();
        $tcUsd = TcSbs::orderBy('fecha', 'desc')->first()->precio;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.orden-compra.publica.lista', get_defined_vars());
    }

    public function obtenerEstadosPortal(Request $request)
    {
        $portal = new PeruComprasHelper();
        $pagina = $portal->parseHtml($portal->visitarUrl('https://www.catalogos.perucompras.gob.pe/ConsultaOrdenesPub/_detalleEstadoOrden?ID_OrdenCompra=' . $request->idOrden));
        try {
            $pagina->find('div[class=modal-header]', 0)->outertext = '';
            $pagina->find('button[id=btnSalir]', 0)->outertext = '';
            $pagina->find('div[class=modal-footer]', 0)->outertext = '';
            $pagina->find('table[id=TablaEstadosOrden]', 0)->class = 'table';
            $pagina->find('table[id=TablaEstadosOrden]', 0)->id = 'tableDetallesPc';
            return $pagina;
        } catch (\ErrorException $ex) {
            return '<div class="text-center">No hay datos disponibles</div>';
        }
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkFecha == 'on') {
            session(['oc_fecha_desde' => $request->fechaDesde]);
            session(['oc_fecha_hasta' => $request->fechaHasta]);
            session(['oc_filtro_fecha' => " (fecha_formalizacion BETWEEN '" .
                Carbon::createFromFormat('d-m-Y', $request->fechaDesde)->toDateString() .
                "' AND '" . Carbon::createFromFormat('d-m-Y', $request->fechaHasta)->toDateString() .
                "')"]);
        } else {
            $request->session()->forget('oc_fecha_desde');
            $request->session()->forget('oc_fecha_hasta');
            $request->session()->forget('oc_filtro_fecha');
        }

        if ($request->chkOrdenesFecha == 'on') {
            session(['oc_ordenes_fecha' => ' fecha_formalizacion IS NOT NULL']);
        } else {
            $request->session()->forget('oc_ordenes_fecha');
        }

        if ($request->chkOdenesProducto == 'on') {
            session(['oc_ordenes_producto' => ' (puntaje_okc>=20 OR puntaje_proy>=20 OR puntaje_smart>=20 OR puntaje_deza>=20 OR puntaje_dorado>=20 OR puntaje_protec>=20)']);
        } else {
            $request->session()->forget('oc_ordenes_producto');
        }

        if ($request->chkCatalogo != null && count($request->chkCatalogo) > 0) {
            session(['oc_catalogos' => "id_catalogo IN (" . implode(',', $request->chkCatalogo) . ")"]);
        } else {
            $request->session()->forget('oc_catalogos');
        }
        return response()->json(array('tipo'=>'success','mensaje'=>'Se han aplicado los filtros'), 200);
    }

    public function obtenerOrdenesPorProducto(Request $request)
    {
        $ordenes = OrdenCompraPublicaDetalle::with('ordenCompraPublica')
            ->join('mgcp_acuerdo_marco.oc_publicas', 'id_orden_compra', '=', 'oc_publicas.id')
            ->join('mgcp_acuerdo_marco.provincias', 'id_provincia', '=', 'provincias.id')
            ->join('mgcp_acuerdo_marco.departamentos', 'id_departamento', '=', 'departamentos.id')
            ->join('mgcp_acuerdo_marco.productos_am', 'id_producto', '=', 'productos_am.id')
            ->join('mgcp_acuerdo_marco.entidades','id_entidad','=','entidades.id')
            ->where('marca', $request->marca)->where('modelo',$request->modelo)->where('part_no', $request->nroParte)
            ->whereNotNull('fecha_formalizacion')
            ->select([
                'fecha_formalizacion', 'entidades.nombre AS nombre_entidad', 'departamentos.nombre AS nombre_departamento', 'razon_social',
                'precio_unitario', 'id_orden_compra', 'cantidad', 'costo_envio', 'plazo_entrega'
        ]);
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'Marca: ' . $request->marca . ', modelo: ' . $request->modelo . ', P/N: ' . $request->nroParte);
        return datatables($ordenes)->toJson();
    }

    public function dataLista(Request $request)
    {
        $ordenes = OrdenCompraPublicaDetalle::with(['ordenCompraPublica', 'ordenCompraPublica.entidad', 'producto'])
            ->join('mgcp_acuerdo_marco.productos_am', 'id_producto', '=', 'productos_am.id')
            ->join('mgcp_acuerdo_marco.categorias', 'productos_am.id_categoria', '=', 'categorias.id')
            ->join('mgcp_acuerdo_marco.oc_publicas', 'id_orden_compra', '=', 'oc_publicas.id')
            ->join('mgcp_acuerdo_marco.provincias', 'id_provincia', '=', 'provincias.id')
            ->join('mgcp_acuerdo_marco.entidades', 'oc_publicas.id_entidad', '=', 'entidades.id')
            ->select([
                'oc_publicas.id', 'fecha_formalizacion', 'entidades.nombre AS nombre_entidad', 'razon_social', 'categorias.descripcion AS categoria',
                'marca', 'modelo', 'productos_am.descripcion AS descripcion_producto','part_no', 'cantidad', 'precio_unitario', 'costo_envio', 'plazo_entrega', 
                'provincias.nombre AS provincia', 'oc_publicas.id_entidad', 'id_orden_compra', 'id_producto', 'id_categoria', 'id_provincia'
        ]);

        if ($request->session()->has('oc_filtro_fecha')) {
            $ordenes = $ordenes->whereRaw(session('oc_filtro_fecha'));
        }
        if ($request->session()->has('oc_ordenes_fecha')) {
            $ordenes = $ordenes->whereRaw(session('oc_ordenes_fecha'));
        }
        if ($request->session()->has('oc_ordenes_producto')) {
            $ordenes = $ordenes->whereRaw(session('oc_ordenes_producto'));
        }
        if ($request->session()->has('oc_catalogos')) {
            $ordenes = $ordenes->whereRaw(session('oc_catalogos'));
        }

        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables($ordenes)->rawColumns(['orden_compra_publica.entidad.semaforo'])->toJson();
    }

    public function proveedoresPortal($tipo)
    {
        $portal = new WebHelper();
        $proveedor = [];
        $acuerdos = [124, 184, 197]; // 2020-5, 2021-2, 2021-6
        $historiales = [];
        
        foreach ($acuerdos as $key => $valor) {
            $filtro = $valor.'^BIENES';
            $data = $portal->enviarData($filtro, 'https://www.catalogos.perucompras.gob.pe/ConsultaOrdenesPub/listarProveedor');
            $filas = explode('¬', $data);
            
            foreach ($filas as $fila) {
                $datax = explode('^', $fila);
                $id_pc = $datax[0];
                $nombre = str_replace('&amp;', '&', $datax[1]);
                
                $queryTotal = OrdenCompraPublicaProveedor::count();
                $query = OrdenCompraPublicaProveedor::where('id_pc', $id_pc)->count();
                if ($query == 0) {
                    $proveedor = new OrdenCompraPublicaProveedor();
                        $proveedor->id_pc = $id_pc;
                        $proveedor->nombre = $nombre;
                    $proveedor->save();
                    array_push($historiales, $proveedor);
                }
                $contador = count($historiales);
            }
        }
        
        if ($tipo == 1) {
            die('***FIN DEL PROCESO. TOTAL DE PROVEEDORES: ' . count($historiales) . '***');
        } else {
            return response()->json(array('descarga' => $contador, 'total' => $queryTotal, 'data' => $historiales), 200);
        }
    }

    public function ceamPortal()
    {
        $portal = new WebHelper();
        // $filtro = ["ClientFilter.Agreement"]["IM-CE-2020-5 COMPUTADORAS DE ESCRITORIO, COMPUTADORAS PORTATILES Y ESCANERES"];
        // $filtro = array(
        //     "ClientFilter" => array("Agreement" => array("IM-CE-2020-5 COMPUTADORAS DE ESCRITORIO, COMPUTADORAS PORTATILES Y ESCANERES")
        //     )
        // );
        // $filtro = {
        //     "ClientFilter" : {
        //         "Agreement": "IM-CE-2020-5 COMPUTADORAS DE ESCRITORIO, COMPUTADORAS PORTATILES Y ESCANERES"
        //     }
        // };
        $filtro = ["Agreement" => "VIGENTE•IM-CE-2020-5 COMPUTADORAS DE ESCRITORIO, COMPUTADORAS PORTATILES Y ESCANERES"];
        $data = $portal->enviarData($filtro, 'https://buscadorcatalogos.perucompras.gob.pe');
        // $data = $portal->visitarUrl('https://buscadorcatalogos.perucompras.gob.pe/Search/Search');
        return $data;
    }

    public function listaOrdenesPublicasAnalisis()
    {
        if (!Auth::user()->tieneRol(135)) {
            return view('mgcp.usuario.sin_permiso');
        }

        LogActividad::registrar(Auth::user(), 'Análisis de O/C públicas', 1);
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $entidades = Entidad::orderBy('nombre', 'asc')->get();
        $proveedores = OrdenCompraPublicaProveedor::orderBy('nombre', 'asc')->get();
        $tcUsd = TcSbs::orderBy('fecha', 'desc')->first()->precio;
        $fechaActual = new Carbon();
        return view('mgcp.orden-compra.publica.analisis', compact('empresas', 'entidades', 'proveedores', 'tcUsd', 'fechaActual'));
    }

    public function dataListaAnalisis(Request $request)
    {
        if (!empty($request->criterio)) {
            $request->session()->put('ocpAnalisisCriterio', $request->criterio);
        } else {
            $request->session()->forget('ocpAnalisisCriterio');
        }

        if ($request->chkFechaOcp == 'on') {
            $request->session()->put('ocpAnalisisFechaDesde', $request->fechaOcpDesde);
            $request->session()->put('ocpAnalisisFechaHasta', $request->fechaOcpHasta);
        } else {
            $request->session()->forget('ocpAnalisisFechaDesde');
            $request->session()->forget('ocpAnalisisFechaHasta');
        }

        if ($request->chkEmpresa == 'on') {
            $request->session()->put('ocpAnalisisEmpresa', $request->selectEmpresa);
        } else {
            $request->session()->forget('ocpAnalisisEmpresa');
        }

        if ($request->chkEntidad == 'on') {
            $request->session()->put('ocpAnalisisEntidad', $request->selectEntidad);
        } else {
            $request->session()->forget('ocpAnalisisEntidad');
        }

        if ($request->chkProveedor == 'on') {
            $request->session()->put('ocpAnalisisProveedor', $request->selectProveedor);
        } else {
            $request->session()->forget('ocpAnalisisProveedor');
        }

        if ($request->chkMarca == 'on') {
            $request->session()->put('ocpAnalisisMarca', $request->ocpMarca);
        } else {
            $request->session()->forget('ocpAnalisisMarca');
        }

        if ($request->chkModelo == 'on') {
            $request->session()->put('ocpAnalisisModelo', $request->ocpModelo);
        } else {
            $request->session()->forget('ocpAnalisisModelo');
        }

        if ($request->chkProcesador == 'on') {
            $request->session()->put('ocpAnalisisProcesador', $request->ocpProcesador);
        } else {
            $request->session()->forget('ocpAnalisisProcesador');
        }

        $this->getConsulta();
        $body = $this->getBodyData($request->pagina);
        $foot = $this->getFooterData($request->pagina);
        if (!is_null($request->criterio)) {
            LogActividad::registrar(Auth::user(), 'Análisis de O/C públicas', 7, null, null, null, 'Criterio: ' . $request->criterio);
        }
        return response()->json(array('body' => $body, 'foot' => $foot), 200);
    }

    public function getConsulta()
    {
        $query = OrdenCompraPublicaAnalisis::select('oc_publicas_analisis.*', 'oc_publicas_analisis.id AS id_ocp', 'entidades.nombre AS entidad', 'empresas.empresa AS empresa', 'productos_am.*', 'oc_publicas_proveedores.nombre AS proveedor')
        ->join('mgcp_acuerdo_marco.entidades', 'oc_publicas_analisis.id_entidad', '=', 'entidades.id')
        ->leftJoin('mgcp_acuerdo_marco.empresas', 'oc_publicas_analisis.id_empresa', '=', 'empresas.id')
        ->leftJoin('mgcp_acuerdo_marco.productos_am', 'oc_publicas_analisis.id_producto', '=', 'productos_am.id')
        ->leftJoin('mgcp_acuerdo_marco.oc_publicas_proveedores', 'oc_publicas_analisis.id_proveedor', '=', 'oc_publicas_proveedores.id');

        if (session()->has('ocpAnalisisCriterio')) {
            $criterio = '%' . str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisCriterio'))) . '%';
            $query = $query->whereRaw('(entidades.nombre LIKE ? OR empresas.empresa LIKE ? OR productos_am.descripcion LIKE ?)', [$criterio, $criterio, $criterio]);
        }

        if (session()->has('ocpAnalisisFechaDesde')) {
            $query = $query->whereBetween('oc_publicas_analisis.fecha', [session()->get('ocpAnalisisFechaDesde'), session()->get('ocpAnalisisFechaHasta')]);
        }
        
        if (session()->has('ocpAnalisisEmpresa')) {
            if (session()->has('ocpAnalisisEmpresa') != null) {
                $query = $query->whereIn('id_empresa', session()->get('ocpAnalisisEmpresa'));
            } else {
                $query = $query->where('id_empresa', '>', 0);
            }
        }

        if (session()->has('ocpAnalisisEntidad')) {
            $query = $query->whereIn('id_entidad', session()->get('ocpAnalisisEntidad'));
        }

        if (session()->has('ocpAnalisisProveedor')) {
            $query = $query->whereIn('id_proveedor', session()->get('ocpAnalisisProveedor'));
        }

        if (session()->has('ocpAnalisisMarca')) {
            $txtMarca = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisMarca'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtMarca]);
        }

        if (session()->has('ocpAnalisisModelo')) {
            $txtModelo = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisModelo'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtModelo]);
        }

        if (session()->has('ocpAnalisisProcesador')) {
            $txtProcesador = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisProcesador'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtProcesador]);
        }
            
        return $query->orderBy('fecha', 'desc');
    }
        
    public function getBodyData($pagina)
    {
        $query = $this->getConsulta()->offset(intval($pagina) <= 1 ? 0 : ((intval($pagina) - 1) * self::TOTAL_FILAS_VISTA))->limit(self::TOTAL_FILAS_VISTA)->get();
        $resultado = '';
        
        foreach ($query as $key) {
            $int_producto = '';
            $ext_producto = '';
            $procesador = '';
            $provee = '';
            $margen = 0;
            $marca = '';
            $modelo = '';
            $part_no = '';
            
            if ($key->id_producto != null) {
                $proInt = Producto::where('id', $key->id_producto)->first();
                $int_marca = $proInt->marca;
                $int_modelo = $proInt->modelo;
                $int_part_no = $proInt->part_no;
                $int_procesador = $proInt->procesador;
                $int_producto = '
                <a title="Ver datos adicionales de producto" data-target="#modalDatosProducto" 
                    data-toggle="modal" href="#" class="producto" data-id="'.$key->id_producto.'">'.$proInt->descripcion.'
                </a>';
            }

            if ($key->id_producto_ext != null) {
                $proExt = Producto::where('id', $key->id_producto_ext)->first();
                $ext_marca = $proExt->marca;
                $ext_modelo = $proExt->modelo;
                $ext_part_no = $proExt->part_no;
                $ext_procesador = $proExt->procesador;
                $ext_producto = 
                '<a title="Ver datos adicionales de producto" data-target="#modalDatosProducto" 
                    data-toggle="modal" href="#" class="producto" data-id="'.$key->id_producto_ext.'">'.$proExt->descripcion.'
                </a>';
            }

            if ($key->id_proveedor != null) {
                $provee = $key->proveedor;
                $margen = number_format($key->margen_ext, 3);
                $marca = $ext_marca;
                $modelo = $ext_modelo;
                $part_no = $ext_part_no;
            } else {
                $provee = $key->empresa;
                $margen = number_format($key->margen, 3);
                $marca = $int_marca;
                $modelo = $int_modelo;
                $part_no = $int_part_no;
            }

            $resultado .= 
            '<div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="table-responsive">
                        <table style="margin-bottom: 0px; font-size: 11.5px" class="table table-condensed ocp">
                            <thead>
                                <tr>
                                    <td style="width: 2%">
                                        <button class="btn btn-primary btn-xs mostrar" data-id="'.$key->id_ocp.'">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </td>
                                    <th class="text-right" style="width: 3%">Fecha: </th>
                                    <td style="width: 4%">' . date('d-m-Y', strtotime($key->fecha)). '</td>
                                    <th class="text-right" style="width: 4%">Entidad: </th>
                                    <td style="width: 10%">' . $key->entidad. '</td>
                                    <th class="text-right" style="width: 4%">Marca: </th>
                                    <td style="width: 5%">' . $marca. '</td>
                                    <th class="text-right" style="width: 4%">Modelo: </th>
                                    <td style="width: 5%">' . $modelo. '</td>
                                    <th class="text-right" style="width: 4%">P/N: </th>
                                    <td style="width: 5%">' . $part_no. '</td>
                                    <th class="text-right" style="width: 4%">Procesador: </th>
                                    <td style="width: 5%">' . $procesador. '</td>
                                    <th class="text-right" style="width: 4%">Cantidad: </th>
                                    <td style="width: 3%">' . $key->cantidad. '</td>
                                    <th class="text-right" style="width: 4%">Proveedor: </th>
                                    <td style="width: 10%">' . $provee. '</td>
                                    <th class="text-right" style="width: 4%">Margen: </th>
                                    <td style="width: 3%">' . $margen. '</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="panel-body" style="display: none">';

                $interno =
                '<div class="panel panel-default mb-3">
                    <div class="panel-heading">
                        <div class="table-responsive">
                            <table style="margin-bottom: 0px;font-size: small" class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th width="15%" class="text-center">Empresa</th>
                                        <th width="45%" class="text-center">Ficha Producto</th>
                                        <th width="10%" class="text-center">Costo</th>
                                        <th width="10%" class="text-center">Precio Unit.</th>
                                        <th width="10%" class="text-center">Total</th>
                                        <th width="10%" class="text-center">Margen</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" style="width: 100%; font-size: small;margin-bottom: 10px;">
                                <tbody>
                                    <tr>
                                        <td width="15%" class="text-center">'.$key->empresa.'</td>
                                        <td width="45%" class="text-center">'.$int_producto.'</td>
                                        <td width="10%" class="text-right">'.number_format($key->precio_costo, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->precio_dolares, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->total, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->margen, 3).'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>';

                $externo =
                '<div class="panel panel-default mb-3">
                    <div class="panel-heading">
                        <div class="table-responsive">
                            <table style="margin-bottom: 0px;font-size: small" class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th width="15%" class="text-center">Proveedor (Competencia)</th>
                                        <th width="45%" class="text-center">Ficha Producto</th>
                                        <th width="10%" class="text-center">Costo</th>
                                        <th width="10%" class="text-center">Precio Unit.</th>
                                        <th width="10%" class="text-center">Total</th>
                                        <th width="10%" class="text-center">Margen</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" style="width: 100%; font-size: small;margin-bottom: 10px;">
                                <tbody>
                                    <tr>
                                        <td width="15%" class="text-center">'.$key->proveedor.'</td>
                                        <td width="45%" class="text-center">'.$ext_producto.'</td>
                                        <td width="10%" class="text-right">'.number_format($key->precio_costo_ext, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->precio_dolares_ext, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->total_ext, 2).'</td>
                                        <td width="10%" class="text-right">'.number_format($key->margen_ext, 3).'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>';

                if ($key->id_empresa != null) {
                    if ($key->id_proveedor > 0) {
                        $resultado .= $interno.$externo;
                    } else {
                        $resultado .= $interno;
                    }
                } else {
                    $resultado .= $externo;
                }
                
            $resultado .= '
            <div class="panel" style="border: 0; box-shadow: none;">
                <div class="panel-body text-center">
                    <button class="btn btn-danger" onclick="editar('. $key->id_ocp .');"><span class="fa fa-pencil"></span> Editar registro</button>
                </div>
            </div>
            </div></div>';
        }
        return $resultado;
    }

    public function getFooterData($pagina)
    {
        $totalFilas = $this->getConsulta()->get()->count();
        $paginas = $totalFilas == 0 ? 0 : ceil($totalFilas / self::TOTAL_FILAS_VISTA);
        $footer = 
        '<div>
            <button title="Anterior" type="button" class="btn btn-default btn-sm anterior">&laquo;</button>
            <div class="btn-group" role="group" style="margin-left: 10px; margin-right: 10px; padding-bottom: 3px"> 
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control input-sm pagina">';

        //Select de páginas   
        if ($totalFilas == 0) {
            $footer .= '<option value="0">0</option>';
        } else {
            for ($i = 1; $i <= $paginas; $i++) {
                $footer .= '<option value="' . $i . '" ' . ($pagina == $i ? 'selected' : '') . '>' . $i . '</option>';
            }
        }

        $footer .= '
                        </select>
                        <div class="form-control-static"> de ' . $paginas . '</div>
                    </div>
                </div>
            </div>
            <button title="Siguiente" type="button" class="btn btn-default btn-sm siguiente">&raquo;</button>
        </div>';
        return $footer;
    }

    /**
     * Analisis de Orden de Compra
     */
    public function registrar(Request $request)
    {
        try {
            $data = OrdenCompraPublicaAnalisis::firstOrNew(['id' => $request->id_ocp]);
                $data->fecha = $request->fecha;
                $data->id_entidad = $request->id_entidad;
                $data->cantidad = $request->cantidad;
                $data->fecha_convocatoria = $request->fecha_convocatoria;
                $data->id_empresa = $request->id_empresa;
                $data->id_producto = $request->id_producto;
                $data->precio_costo = str_replace(',', '', $request->costo);
                $data->precio_dolares = str_replace(',', '', $request->precio_dol);
                $data->precio_soles = str_replace(',', '', $request->precio_sol);
                $data->total = str_replace(',', '', $request->total);
                $data->margen = str_replace('%', '', $request->margen);
                $data->id_proveedor = $request->id_proveedor;
                $data->id_producto_ext = $request->id_producto_ext;
                $data->precio_costo_ext = str_replace(',', '', $request->costo_ext);
                $data->precio_dolares_ext = str_replace(',', '', $request->precio_dol_ext);
                $data->precio_soles_ext = str_replace(',', '', $request->precio_sol_ext);
                $data->total_ext = str_replace(',', '', $request->total_ext);
                $data->margen_ext = str_replace('%', '', $request->margen_ext);
            $data->save();

            $response = 'ok';
            $alert = 'success';
            if ($request->id_ocp > 0) {
                $message = 'Se ha editado la OC Pública con éxito';
            } else {
                $message = 'Se ha registrado la OC Pública con éxito';
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

    public function editar(Request $request)
    {
        $data = OrdenCompraPublicaAnalisis::find($request->id);
        return response()->json($data, 200);
    }

    public function exportar()
    {
        return Excel::download(new AnalisisOrdenCompraExport, 'reporte_analisis_oc.xlsx');
    }
}
