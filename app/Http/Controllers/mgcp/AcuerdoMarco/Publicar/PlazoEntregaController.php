<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Helpers\mgcp\CuadroCosto\RequerimientoHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use Illuminate\Support\Facades\Auth;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\Usuario\LogActividad;

class PlazoEntregaController extends Controller
{
    private $nombreFormulario = 'Publicar plazos de entrega';

    public function index(Request $request)
    {
        if (!Auth::user()->tieneRol(64)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $regiones = Departamento::orderBy('nombre', 'asc')->get();
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.publicar.plazos-entrega', get_defined_vars());
    }

    public function obtenerCategoriasPorAcuerdo(Request $request)
    {
        $categorias = Categoria::join('mgcp_acuerdo_marco.catalogos','catalogos.id','id_catalogo')->join('mgcp_acuerdo_marco.acuerdo_marco','acuerdo_marco.id','id_acuerdo_marco')
        ->where('activo',true)->where("acuerdo_marco.descripcion",substr($request->descripcionAm,0,strpos($request->descripcionAm,' ')))->select(['categorias.descripcion AS categoria','categorias.id','acuerdo_marco.descripcion AS acuerdo_marco'])->orderBy('categorias.id')->get();
        return response()->json($categorias);
    }

    public function obtenerProductosporCategoria(Request $request)
    {
        $productos = Producto::whereIn('id_categoria', $request->categoria)->get();
        return response()->json($productos);
    }

    public function procesar(Request $request)
    {
        set_time_limit(80);
        $listaEnviar = [];
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();

        if (!$portal->login($empresa, 1)) { /// Revisar el uso de multiusuario (Estaba en el 3)
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'No se pudo iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo'), 200);
        } else {
            //$producto = ($request->tipoProducto == 1) ? Producto::find($request->descripcion)->part_no : $request->descripcion;
            $categoria = Categoria::find($request->idCategoria);
            $resultado = $portal->enviarData($request->idAcuerdo.'^'.$categoria->catalogo->id_pc.'^'.$categoria->id_pc.'^'.$request->descripcion.'^'.$request->idProvincia, 'https://www.catalogos.perucompras.gob.pe/MejoraPlazo/consultaMejoraPlazoEntrega');
            $lista = explode("¯", $resultado);
            $productos = explode("¬", $lista[1]);

            foreach ($productos as $producto) {
                $campos = explode("^", $producto);
                if (count($campos) > 7 && $campos[6] != $request->plazo && $campos[7] != $request->plazo) {
                    array_push($listaEnviar, $campos[0] . "^" . $request->plazo . "^");
                }
            }
        }

        $mensaje = (count($listaEnviar) == 0) ? 'OK' : $portal->enviarData(implode("¬", $listaEnviar), 'https://www.catalogos.perucompras.gob.pe/MejoraPlazo/modificarMejoraPlazoEntrega');
        if ($mensaje == 'OK') {
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Procesado'));
        } else {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Problema al procesar: ' . $mensaje));
        }
    }

    public function testPlazos(Request $request)
    {
        /*$request->idEmpresa = 6;
        $request->idAcuerdo=43764;//EQUIPOS MULTIMEDIA
        $request->idCategoria=137; //PROYECTOR ALTO BRILLO
        $request->idProvincia='010400'; //AMAZONAS
        $request->plazo=45;*/

        // $idEmpresa = 1;
        // $idAcuerdo = '124-82351-2-90'; //
        // $idCategoria = 123; // COMPUTADORAS PORTATILES (11059) --> CATALOGO (126)
        // $idDepartamento = '040000'; //AREQUIPA
        // $idProvincia = '040100'; //AREQUIPA
        // $plazo = 45;
        // $descripcion = 'RUGGED';

        // $idEmpresa = $request->idEmpresa;
        // $idAcuerdo = $request->idAcuerdo; //
        // $idCategoria = $request->idCategoria; // COMPUTADORAS PORTATILES (11059) --> CATALOGO (126)
        // //$idDepartamento = '040000'; //AREQUIPA
        // $idProvincia = $request->idProvincia;
        // $plazo = $request->plazo;
        // $descripcion = $request->descripcion;

        /**
         * 82351^126^11059^^040100
         * 
         * 82351 ^ 126 ^ 11059 ^ 2PTL342-5812SOH3-N3-4-FHD ^ 040100
         * 83568 ^ 186 ^ 11317 ^ V11H978021 ^ 180300
         * ACUERDO / CATEGORIA->CATALOGO->ID_PC / CATEGORIA->ID_PC / DESCRIPCION
         */

        set_time_limit(80);
        $listaEnviar = [];
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 3
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'No se pudo iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo'), 200);
        } else {
            $categoria = Categoria::find($request->idCategoria);
            $resultado = $portal->enviarData($request->idAcuerdo.'^'.$categoria->catalogo->id_pc.'^'.$categoria->id_pc.'^'.$request->descripcion.'^'.$request->idProvincia, 'https://www.catalogos.perucompras.gob.pe/MejoraPlazo/consultaMejoraPlazoEntrega');
            $lista = explode("¯", $resultado);
            $productos = explode("¬", $lista[1]);
            // foreach ($productos as $producto) {
            //     $campos = explode("^", $producto);
            //     if (count($campos) > 7 && $campos[6] != $request->plazo && $campos[7] != $request->plazo)
            //     {
            //         array_push($listaEnviar, $campos[0]."^".$request->plazo."^".$request->descripcion);
            //     }
            // }
        }
        return response()->json($productos, 200);
    }
}
