<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Descarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Helpers\mgcp\ProductoHelper;
use App\Models\mgcp\AcuerdoMarco\Producto\DescargaProductoAdjudicado;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\Auth;

/**
 * Utilizado para descargar y actualizar los productos adjudicados del portal Perú Compras
 * 
 * Primero se descargan los productos de una empresa seleccionada por el usuario, luego
 * se registran o actualizan en la base de datos del sistema
 * 
 * @author Wilmar Garibaldi Valdez <wgaribaldi@okcomputer.com.pe>
 */

class ProductoAdjudicadoController extends Controller 
{
    private $nombreFormulario = 'Descarga de productos adjudicados';

    public function index() {
        if (!Auth::user()->tieneRol(43)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.descarga.productos_adjudicados')->with(compact('empresas'));
    }

    public function obtenerProductos(Request $request) {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3); // ealvarez 3
        $url = "https://www.catalogos.perucompras.gob.pe/Reportes/_detProductoOfertadoIndex?N_Acuerdo=" . $request->idAcuerdo . "&N_Catalogo=" . $request->idCatalogo . "&N_Categoria=" . $request->idCategoria . "&C_Descripcion=";
        $pagina = $portal->visitarUrl($url);
        echo $pagina;
    }

    private function registrarProductoDescargado($request)
    {
        $descarga = new DescargaProductoAdjudicado();
            $descarga->id_empresa = $request->idEmpresa;
            $descarga->id_categoria = $request->idCategoria;
            $descarga->id_pc = $request->idPc;
            $descarga->imagen = $request->imagen;
            $descarga->descripcion = $request->descripcion;
            $descarga->ficha = $request->ficha;
            $descarga->moneda = $request->moneda;
            $descarga->precio = trim($request->precio);
            $descarga->estado = $request->estado;
            $descarga->puntaje = $request->puntaje ?? 0;
        $descarga->save();
    }

    public function procesar(Request $request) {
        $this->registrarProductoDescargado($request);
        $categoria = Categoria::where('id_pc', $request->idCategoria)->first();
        $empresa = Empresa::find($request->idEmpresa);
        $campoId = 'id_' . $empresa->nombre_corto;
        $campoPrecio = 'precio_' . $empresa->nombre_corto;
        $campoPuntaje = 'puntaje_' . $empresa->nombre_corto;
        $producto = Producto::where('id_categoria', $categoria->id)->where('ficha_tecnica',$request->ficha)->orderBy('id','desc')->first();
            if (is_null($producto)) {
                $producto = new Producto();
                ProductoHelper::procesarDescripcion($producto, $request->descripcion);
                //Debe obtener marca, modelo, nro parte
            }
            $producto->imagen = $request->imagen;
            $producto->ficha_tecnica = $request->ficha;
            $producto->moneda = trim($request->moneda);
            $producto->id_categoria = $categoria->id;
            $producto->$campoId = $request->idPc;
            $producto->$campoPrecio = trim($request->precio);
            $producto->$campoPuntaje = $request->puntaje ?? 0;
        $producto->save();
        return response()->json(array('mensaje' => 'procesado'), 200);
    }

}
