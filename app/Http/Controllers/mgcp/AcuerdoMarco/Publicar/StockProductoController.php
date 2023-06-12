<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Helpers\mgcp\AcuerdoMarco\StockHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use Illuminate\Support\Facades\Auth;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Producto\NroParteIgnorado;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\StockEmpresaPublicar;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockProductoController extends Controller
{
    private $nombreFormulario = 'Publicar stock de productos';

    public function index()
    {
        if (!Auth::user()->tieneRol(52)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        return view('mgcp.acuerdo-marco.publicar.stock-productos', get_defined_vars());
    }

    public function publicarStockPorProformas($idEmpresa)
    {
        $fechaActual = new Carbon();
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        $empresa = Empresa::find($idEmpresa);
        $campoPrecio = 'precio_' . $empresa->nombre_corto;
        $productos = Producto::with('categoria', 'categoria.catalogo', 'categoria.catalogo.acuerdoMarco')
            ->whereRaw("id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_gran_compra WHERE (fecha_emision = ? OR fecha_limite = ?) AND id_empresa = ? 
            UNION SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria 
            WHERE (fecha_emision = ? OR fecha_limite = ?) AND id_empresa = ?)", [$fechaActual, $fechaActual, $idEmpresa, $fechaActual, $fechaActual, $idEmpresa])->get();
        foreach ($productos as $producto) {
            $stockEspecifico = StockEmpresaPublicar::where('id_producto', $producto->id)->where('id_empresa', $empresa->id)->first();
            $stockPublicar = is_null($stockEspecifico) ? StockHelper::calcularCantidad($producto->$campoPrecio, $producto->moneda, $tipoCambio) : $stockEspecifico->stock;
            echo 'Empresa: ' . $empresa->empresa . ', producto: ' . $producto->descripcion, '<br>Resultado: ' . StockHelper::publicar($empresa, $producto, $stockPublicar, $stockPublicar, true)['mensaje'] . '<hr>';
        }
        die('***FIN DEL PROCESO. TOTAL DE PRODUCTOS: ' . $productos->count() . '***');
    }

    public function procesar(Request $request)
    {
        $producto = Producto::where('descripcion', trim($request->descripcion))->first();
        if (!is_null($producto)) {
            $ignorar = NroParteIgnorado::find($producto->part_no);
            if (!is_null($ignorar)) {
                return response()->json(array('mensaje' => 'Ignorado por nro. de parte', 'tipo' => 'warning'), 200);
            }
        }

        if ($producto->nuevo) {
            $empresa = Empresa::find($request->idEmpresa);
            $portal = new PeruComprasHelper();
    
            $portal->login($empresa, 3); // ealvarez 3
            $url = 'https://www.catalogos.perucompras.gob.pe/MejoraBasica/ModificarStock';
            $dataEnviar = array();
            $dataEnviar['ID_ProductoOfertado'] = $request->idPc;
            $dataEnviar['N_Acuerdo'] = $request->acuerdo;
            $dataEnviar['N_Catalogo'] = $request->catalogo;
            $dataEnviar['N_Categoria'] = $request->categoria;
            $dataEnviar['N_StockAnt'] = $request->stockAnterior;
            $dataEnviar['N_Stock'] = $request->stockPublicar;
            $dataEnviar['__RequestVerificationToken'] = $portal->token;
            $resultado = $portal->parseHtml($portal->enviarData($dataEnviar, $url));
            $mensaje = $resultado->find('div[id=MensajeModal] div.modal-body', 0)->plaintext;
            $portal->finalizar();
            if (strpos($mensaje, 'satisfactoriamente') !== false) {
                return response()->json(array('mensaje' => 'Actualizado', 'tipo' => 'success'), 200);
            } else {
                return response()->json(array('mensaje' => 'Error: ' . $mensaje, 'tipo' => 'danger'), 200);
            }
        } else {
            return response()->json(array('mensaje' => 'Producto de incorporación pasada', 'tipo' => 'warning'), 200);
        }
    }

    public function obtenerProductos(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3); // ealvarez 3
        $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/_ListaProductosOfertados?N_Acuerdo=" . $request->idAcuerdo . "&N_Catalogo=" . $request->idCatalogo . "&N_Categoria=" . $request->idCategoria . "&C_Descripcion=";

        $pagina = $portal->visitarUrl($url);
        $pagina = str_replace(['<?xml version="1.0" encoding="UTF-8"?>', "$('.fancybox').fancybox();"], "", $pagina);
        echo $pagina;
    }

    public function testStock()
    {
        $idEmpresa = 1;
        $data = [];

        $fechaActual = new Carbon('2022-06-15');
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        $empresa = Empresa::find($idEmpresa);
        $campoPrecio = 'precio_' . $empresa->nombre_corto;
        $productos = Producto::with('categoria', 'categoria.catalogo', 'categoria.catalogo.acuerdoMarco')
            ->whereRaw("id IN (SELECT id_producto FROM mgcp_acuerdo_marco.proformas_gran_compra WHERE (fecha_emision = ? OR fecha_limite = ?) AND id_empresa = ? 
            UNION SELECT id_producto FROM mgcp_acuerdo_marco.proformas_compra_ordinaria 
            WHERE (fecha_emision = ? OR fecha_limite = ?) AND id_empresa = ?)", [$fechaActual, $fechaActual, $idEmpresa, $fechaActual, $fechaActual, $idEmpresa])->get();
        foreach ($productos as $producto) {
            $stockEspecifico = StockEmpresaPublicar::where('id_producto', $producto->id)->where('id_empresa', $empresa->id)->first();
            $stockPublicar = is_null($stockEspecifico) ? StockHelper::calcularCantidad($producto->$campoPrecio, $producto->moneda, $tipoCambio) : $stockEspecifico->stock;
            $array = ['id_producto' => $producto->id, 'part_no' => $producto->part_no, 'stock' => $stockPublicar, 'estado' => $producto->descontinuado];
            array_push($data, $array);
            //echo 'Empresa: ' . $empresa->empresa . ', producto: ' . $producto->descripcion, '<br>Resultado: ' . StockHelper::publicar($empresa, $producto, $stockPublicar, $stockPublicar, true)['mensaje'] . '<hr>';
        }
        return response()->json($data, 200);
    }
}
