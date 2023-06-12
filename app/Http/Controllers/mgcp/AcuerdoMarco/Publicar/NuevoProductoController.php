<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Http\Controllers\Controller;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\PublicarProducto;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NuevoProductoController extends Controller
{
    private $nombreFormulario = 'Publicar nuevos productos';

    public function index()
    {
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.publicar.nuevos-productos', get_defined_vars());
    }

    public function confirmarPortal($idEmpresa, $tipo)
    {
        $empresa = Empresa::find($idEmpresa);
        $filas = PublicarProducto::join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', 'id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', 'id_catalogo')->where('id_empresa', $empresa->id)
            ->select(['categorias.id_pc AS id_categoria', 'catalogos.id_pc AS id_catalogo'])->distinct()->get();

        foreach ($filas as $fila) {
            $reintentar = true;
            $portal = new PeruComprasHelper();
            while ($reintentar) {
                if ($portal->login($empresa, 3)) { // ealvarez 3
                    $reintentar = false;
                }
            }

            $dataEnviar = array();
            $dataEnviar['N_Catalogo'] = $fila->id_catalogo;
            $dataEnviar['N_Categoria'] = $fila->id_categoria;
            $dataEnviar['__RequestVerificationToken'] = $portal->token;
            
            if ($tipo == 0) {
                $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertado/Envia_ProductoOfertadoTMP';
            } else {
                $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertadoAmp/Envia_ProductoOfertadoTMP';
            }
            $portal->enviarData($dataEnviar, $url);
        }
    }

    public function procesarArchivo(Request $request)
    {
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray();
            $contador = 1;
            foreach ($data as $row) {
                //Ignora la primera fila porque contiene cabeceras
                if ($contador == 1) {
                    $contador++;
                    continue;
                }
                $existe = PublicarProducto::where('id_empresa', $request->empresa)->where('id_producto', $row[0])->where('tipo', $request->tipo)->first();
                if ($existe == null) {
                    $registrar = new PublicarProducto();
                    $registrar->id_empresa = $request->empresa;
                    $registrar->id_producto = $row[0];
                    $registrar->tipo = $request->tipo;
                    $registrar->precio = $row[1];
                    $registrar->publicado = 0;
                    $registrar->save();
                }
            }
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Archivo ' . $file->getClientOriginalName() . ' procesado'), 200);
        } else {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Seleccione un archivo antes de continuar'), 200);
        }
    }

    public function procesar(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $productoPublicar = PublicarProducto::find($request->id);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 3
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'error'), 200);
        } else {
            switch ($request->tipo) {
                case 0:
                    $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertado/Inserta_ProductoOfertadoTMP';
                    break;
                case 1:
                    $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertadoAmp/Inserta_ProductoOfertadoTMP';
                    break;
            }
            $dataEnviar = array();
            $dataEnviar['N_CatalogoProducto'] = $request->idPc;
            $dataEnviar['C_MonedaOfertada'] = $request->moneda;
            $dataEnviar['N_PrecioOfertado'] = $request->precio;
            $dataEnviar['__RequestVerificationToken'] = $portal->token;
            $portal->enviarData($dataEnviar, $url);
            $productoPublicar->publicado = true;
            $productoPublicar->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'procesado'), 200);
        }
    }

    public function obtenerProductos(Request $request)
    {
        $productos = PublicarProducto::join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->where('id_empresa', $request->idEmpresa)->where('tipo', $request->tipo)
            ->where('publicado', false)
            ->select('publicar_precios.id', 'productos_am.id_pc', 'productos_am.descripcion', 'precio', 'moneda')
            ->orderBy('publicar_precios.id')->get();
        $contador = 1;
        $datos = '';
        foreach ($productos as $producto) {
            $datos .= '<tr>';
            $datos .= '<td class="text-center">' . $contador . '</td>';
            $datos .= '<td class="text-center id">' . $producto->id . '</td>';
            $datos .= '<td class="text-center idPc">' . $producto->id_pc . '</td>';
            $datos .= '<td>' . $producto->descripcion . '</td>';
            $datos .= '<td class="text-right precio">' . $producto->precio . '</td>';
            $datos .= '<td class="text-center moneda">' . $producto->moneda . '</td>';
            $datos .= '<td class="resultado text-center"></td>';
            $datos .= '</tr>';
            $contador++;
        }
        echo $datos;
    }
}
