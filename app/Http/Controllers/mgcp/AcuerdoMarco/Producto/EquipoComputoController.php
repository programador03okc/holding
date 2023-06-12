<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Producto;

use App\Helpers\mgcp\AcuerdoMarco\EquipoComputoFiltroHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Producto\HistorialActualizacion;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Helpers\mgcp\PeruComprasHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EquipoComputoController extends Controller
{
    public function busqueda()
    {
        if (!Auth::user()->tieneRol(39)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $helper = new EquipoComputoFiltroHelper();
        $marcas=$helper->marcas();
        //$almacenamientos=$helper->almacenamientos();
        $categorias=$helper->categorias();
        $sistemasOperativos=$helper->sistemasOperativos();
        $pantallaResoluciones=$helper->pantallaResoluciones();
        $procesadorModelos=$helper->procesadorModelos();
        //$capacidadRam=$helper->capacidadRam();
        return view('mgcp.acuerdo-marco.producto.equipo-computo.busqueda')->with(compact('marcas','categorias','sistemasOperativos',
        'pantallaResoluciones','procesadorModelos','empresas'));
    }

    public function dataBusqueda(Request $request)
    {
        $productos = Producto::join('mgcp_acuerdo_marco.categorias', 'id_categoria', '=', 'categorias.id')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'id_catalogo')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', '=', 'catalogos.id_acuerdo_marco')
            ->select(['productos_am.id', 'acuerdo_marco.descripcion AS acuerdo_marco', 'productos_am.descripcion', 'moneda', 'precio_okc', 'puntaje_okc','marca','modelo','part_no',
                'precio_proy', 'puntaje_proy', 'precio_smart', 'puntaje_smart', 'precio_deza', 'puntaje_deza', 'precio_dorado', 'puntaje_dorado', 'precio_protec','puntaje_protec','imagen', 'ficha_tecnica'])->whereRaw('activo=true');

        if ($request->session()->has('prod_catalogos')) {
            $productos = $productos->whereRaw(session('prod_catalogos'));
        }

        if ($request->session()->has('prod_adjudicados')) {
            $productos = $productos->whereRaw("(puntaje_okc>=20 OR puntaje_proy>=20 OR puntaje_smart>=20 OR puntaje_deza>=20 OR puntaje_dorado>=20 OR puntaje_protec>=20)");
        }

        $criterio=str_replace('*', '%', strtoupper(strtoupper($request->search['value'])));
        $productos=$productos->whereRaw("(productos_am.descripcion LIKE ? OR productos_am.marca LIKE ? OR productos_am.modelo LIKE ?)", ["%{$criterio}%","%{$criterio}%","%{$criterio}%"]);
        return datatables($productos)->toJson();
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkBusquedaLibre == 'on') {
            session(['prod_busqueda_libre' => true]);
        } else {
            $request->session()->forget('prod_busqueda_libre');
        }

        if ($request->chkActivos == 'on') {
            session(['prod_activos' => true]);
        } else {
            $request->session()->forget('prod_activos');
        }

        if ($request->chkAdjudicados == 'on') {
            session(['prod_adjudicados' => true]);
        } else {
            $request->session()->forget('prod_adjudicados');
        }

        //Catalogos
        if ($request->chkCatalogo != null && count($request->chkCatalogo) > 0) {
            $catalogos = " (";
            foreach ($request->chkCatalogo as $checkCatalogo) {
                $catalogos .= "id_catalogo=" . $checkCatalogo . " OR ";
            }
            session(['prod_catalogos' => substr($catalogos, 0, -4) . ")"]);
        } else {
            $request->session()->forget('prod_catalogos');
        }
        return response()->json(array('tipo'=>'success','mensaje'=>'Se han aplicado los filtros'), 200);
    }

    public function obtenerPrecioStockPortal(Request $request)
    {
        $producto = Producto::find($request->idProducto);
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 2
            return response()->json(array('empresa' => $request->idEmpresa,'tipo'=>'danger' ,'valor' => 'Problema al iniciar sesión'), 200);
        }

        $campoId = 'id_' . $empresa->nombre_corto;
        $campoPuntaje = 'puntaje_' . $empresa->nombre_corto;
        $idPortal = $producto->$campoId;
        $puntaje = $producto->$campoPuntaje;

        if ($puntaje < 20 || $puntaje == null) {
            return response()->json(array('empresa' => $request->idEmpresa,'tipo'=>'danger' ,'valor' => 'No adjudicado'), 200);
        }
        $url = "";
        switch ($request->tipo) {
            case 'stock':
                $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/ModificarStock?ID_ProductoOfertado=";
                break;
            case 'precio':
                $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/ReducirPrecio?ID_ProductoOfertado=";
                break;
        }

        $pagina = $portal->visitarUrl($url . $idPortal);
        //$portal->finalizarCurl();
        $resultado = $portal->parseHtml($pagina)->find('input', 6);
        if (gettype($resultado) == "NULL") {
            return response()->json(array('empresa' => $request->idEmpresa,'tipo'=>'danger' ,'valor' => 'Problema al leer del portal'), 200);
        } else {
            $valor = $resultado->value;
        }
        switch ($request->tipo) {
            case 'stock':
                $valor = number_format($valor, 0, '.', ',');
                break;
            case 'precio':
                $valor = ($producto->moneda == 'USD' ? '$' : 'S/') . number_format($valor, 2, '.', ',');
                break;
        }
        return response()->json(array('empresa' => $request->idEmpresa, 'tipo'=>'success','valor' => $valor), 200);
    }

    public function actualizarPrecioStockPortal(Request $request)
    {
        $nuevoValor = str_replace(['$', ' ', ',', 'S/'], '', $request->nuevoValor);
        $valorActual = str_replace(['$', ' ', ',', 'S/'], '', $request->valorActual);
        if ($nuevoValor == '' || !is_numeric($nuevoValor) || $nuevoValor < 0) {
            return response()->json(array('empresa' => $request->idEmpresa, 'tipo' => 'danger', 'mensaje' => 'El nuevo valor debe ser un número'), 200);
        }
        if ($request->comentario == '') {
            return response()->json(array('empresa' => $request->idEmpresa, 'tipo' => 'danger', 'mensaje' => 'Ingrese un comentario antes de continuar'), 200);
        }
        $empresa = Empresa::find($request->idEmpresa);
        $producto = Producto::find($request->idProducto);
        $categoria = Categoria::find($producto->id_categoria);
        $catalogo = Catalogo::find($categoria->id_catalogo);
        $acuerdo = AcuerdoMarco::find($catalogo->id_acuerdo_marco);
        
        $campoId = 'id_' . $empresa->nombre_corto;
        $campoPrecio = 'precio_' . $empresa->nombre_corto;
        $portal = new PeruComprasHelper();

        if (!$portal->login($empresa, 3)) { // ealvarez 2
            return response()->json(array('empresa' => $request->idEmpresa, 'tipo' => 'danger', 'mensaje' => 'Problema al iniciar sesión'), 200);
        }

        $dataEnviar = array();
        $dataEnviar['ID_ProductoOfertado'] = $producto->$campoId;
        $dataEnviar['N_Acuerdo'] = $acuerdo->id_pc;
        $dataEnviar['N_Catalogo'] = $catalogo->id_pc;
        $dataEnviar['N_Categoria'] = $categoria->id_pc;
        switch ($request->tipoActualizacion) {
            case 'precio':
                $dataEnviar['N_PrecioOfertadoAnt'] = $valorActual;
                $dataEnviar['N_PrecioOfertado'] = $nuevoValor;
                $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/ReducirPrecio";
                break;
            case 'stock':
                $dataEnviar['N_StockAnt'] = $valorActual;
                $dataEnviar['N_Stock'] = $nuevoValor;
                $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/ModificarStock";
                break;
        }
        $dataEnviar['__RequestVerificationToken'] = $portal->token;

        $mensaje = $portal->parseHtml($portal->enviarData($dataEnviar, $url))->find('div[id=MensajeModal] div.modal-body', 0)->plaintext;
        //$portal->finalizarCurl();
        //die($mensaje);
        if (strpos($mensaje, 'satisfactoria') !== false) {
            $historial = new HistorialActualizacion;
            $historial->id_usuario = Auth::user()->id;
            $historial->id_producto = $producto->id;
            $historial->id_empresa = $empresa->id;
            $historial->fecha = new Carbon();
            if ($request->tipoActualizacion == 'precio') {
                $precioAnterior = $producto->$campoPrecio;
                $producto->$campoPrecio = $nuevoValor;
                $producto->save();
                $historial->detalle = 'Precio anterior: ' . $precioAnterior . ', nuevo precio: ' . ($producto->moneda == 'USD' ? '$' : 'S/') . number_format($nuevoValor, 2);
            } else {
                $historial->detalle = 'Nuevo stock: ' . $nuevoValor;
            }
            $historial->comentario = $request->comentario;
            $historial->save();
            return response()->json(array('empresa' => $request->idEmpresa, 'tipo' => 'success', 'mensaje' => 'Actualizado'), 200);
        } else {
            return response()->json(array('empresa' => $request->idEmpresa, 'tipo' => 'danger', 'mensaje' => 'Error al actualizar: ' . $mensaje), 200);
        }
    }

    public function obtenerDetallesPorId(Request $request)
    {
        $producto = Producto::find($request->idProducto);
        return response()->json($producto, 200);
    }

    public function obtenerDetallesPorMMN(Request $request)
    {
        $producto = Producto::where('marca',$request->marca)->where('modelo',$request->modelo)->where('part_no',$request->nro_parte)->orderBy('id','desc')->first();
        return response()->json($producto, 200);
    }
   
}
