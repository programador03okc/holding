<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Descarga;

use App\Http\Controllers\Controller;
use App\Helpers\mgcp\ProductoHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\Usuario\LogActividad;

class NuevoProductoController extends Controller
{
    private $nombreFormulario = 'Descargar proformas';
    const INDICE_PAGINA = 0;

    public function index() {
        if (!Auth::user()->tieneRol(42)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        $indicePagina = self::INDICE_PAGINA;
        return view('mgcp.acuerdo-marco.descarga.nuevos_productos', get_defined_vars());
    }

    public function obtenerProductos(Request $request) {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 2
            echo '<tr><td colspan="7" class="text-center">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</td></tr>';
        } else {
            $dataEnviar = $this->generarCamposDatatable($request->pagina);
            $dataEnviar['N_Acuerdo'] = $request->idAcuerdo;
            $dataEnviar['N_Catalogo'] = $request->idCatalogo;
            $dataEnviar['N_Categoria'] = $request->idCategoria;
            $dataEnviar['__RequestVerificationToken'] = $portal->token;
            $url = '';
            switch ($request->tipoProforma) {
                case 'productos_nuevo_acuerdo':
                    $url = "https://www.catalogos.perucompras.gob.pe/t_ProductoOfertado/_CatalogoProductoIndexJson";
                    break;
                case 'productos_acuerdo_vigente':
                    $url = "https://www.catalogos.perucompras.gob.pe/t_ProductoOfertadoAmp/_CatalogoProductoIndexJson";
                    break;
            }

            $resultado = $portal->enviarData($dataEnviar,$url);
            $productos = json_decode($resultado);
            
            $datos = '';
            $contador = 1;
            foreach ($productos->data as $producto) {
                $datos .= '<tr>';
                $datos .= '<td class="text-center">' . $contador . '</td>';
                $datos .= '<td class="text-center idPc">' . $producto->N_CatalogoProducto . '</td>'; //ID
                $datos .= '<td class="text-center descripcion">' . $producto->C_Descripcion . '</td>';
                $datos .= '<td class="text-center imagen">' . $producto->C_Imagen . '</td>';
                $datos .= '<td class="text-center ficha">' . $producto->C_ArchivoDescriptivo . '</td>';
                $datos .= '<td class="text-center moneda">' . $producto->C_MonedaOfertada . '</td>';
                $datos .= '<td class="text-center resultado"></td>';
                $datos .= '</tr>';
                $contador++;
            }
            echo $datos;
        }
    }

    public function procesar(Request $request) {
        $categoria = Categoria::where('id_pc', $request->categoria)->first();
        $producto = Producto::where('id_pc', $request->idPc)->first();
            if (is_null($producto)) {
                $producto = new Producto();
                $producto->id_categoria = $categoria->id;
            }
            $producto->imagen = 'https://saeusceprod01.blob.core.windows.net/contproveedor/Imagenes/Productos/' . $request->imagen;
            $producto->ficha_tecnica = 'https://saeusceprod01.blob.core.windows.net/contproveedor/Documentos/Productos/' . $request->ficha;
            $producto->moneda = $request->moneda;
            ProductoHelper::procesarDescripcion($producto, $request->descripcion);
            $producto->id_pc = $request->idPc;
            $producto->nuevo = true;
            $producto->activo = true;
            $producto->descontinuado = false;
        $producto->save();
        return response()->json(array('mensaje' => 'procesado'), 200);
    }

    private function generarCamposDatatable($pagina) {
        $totalFilas = 2000;
        $dataEnviar = array();
        $dataEnviar['draw'] = 1;

        $dataEnviar['C_Descripcion'] = '';
        $dataEnviar['start'] = $pagina*$totalFilas;
        $dataEnviar['length'] = $totalFilas;

        $dataEnviar['search'] = array('value' => '', 'regex' => 'false');

        $dataEnviar['columns'][] = array('data' => 'C_Imagen', 'name' => 'C_Imagen',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['columns'][] = array('data' => 'C_Descripcion', 'name' => 'C_Descripcion',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['columns'][] = array('data' => 'C_ArchivoDescriptivo', 'name' => 'C_ArchivoDescriptivo',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['columns'][] = array('data' => 'C_MonedaOfertada', 'name' => 'C_MonedaOfertada',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['columns'][] = array('data' => 'N_PrecioOfertado', 'name' => 'N_PrecioOfertado',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['columns'][] = array('data' => 'N_CatalogoProducto', 'name' => 'N_CatalogoProducto',
            'searchable' => 'true', 'orderable' => 'true', 'search' => array('value' => '', 'regex' => 'false'));

        $dataEnviar['order'][] = array('dir' => 'asc', 'column' => '0');
        return $dataEnviar;
    }

}
