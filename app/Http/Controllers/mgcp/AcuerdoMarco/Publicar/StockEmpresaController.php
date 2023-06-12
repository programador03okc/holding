<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Helpers\mgcp\AcuerdoMarco\StockHelper;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\StockEmpresaPublicar;
use App\Models\mgcp\Usuario\LogActividad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StockEmpresaController extends Controller
{
    private $nombreFormulario = 'Publicar stock por empresa';

    public function index()
    {
        if (!Auth::user()->tieneRol(52)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.publicar.stock-empresa', get_defined_vars());
    }

    public function descargarPlantilla()
    {
        return Storage::download('mgcp/plantillas/plantilla-stock-empresa.xlsx');
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
                $stock = StockEmpresaPublicar::where('id_empresa', $request->empresa)->where('id_producto', $row[0])->first() ?? new StockEmpresaPublicar();
                $stock->id_empresa = $request->empresa;
                $stock->id_producto = $row[0];
                $stock->stock = $row[1];
                $stock->publicado = false; //Se asume que se va a procesar de nuevo si se sube el archivo
                $stock->save();
            }
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Archivo ' . $file->getClientOriginalName() . ' procesado'), 200);
        } else {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Seleccione un archivo antes de continuar'), 200);
        }
    }

    public function enviarPortal(Request $request) {
        try {
            $empresa = Empresa::find($request->idEmpresa);
            $producto = Producto::find($request->idProducto);
            $stock = StockEmpresaPublicar::where('id_empresa',$request->idEmpresa)->where('id_producto',$request->idProducto)->first();
            $stock->publicado = true;
            $stock->save();
            return response()->json(StockHelper::publicar($empresa,$producto,$request->stock,$request->stock,true), 200);
        } catch (Exception $th) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Error de envío', 'error' => $th), 200);
        }
    }

    public function obtenerProductos(Request $request)
    {
        $productos = StockEmpresaPublicar::join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->where('id_empresa', $request->idEmpresa)->where('publicado', false)
            ->select('stock_empresas_publicar.id', 'id_producto', 'productos_am.descripcion', 'stock')->get();
        $contador = 1;

        if ($productos->count() == 0) {
            return '<tr><td colspan="5" class="text-center">Sin productos a publicar para la empresa seleccionada</td></tr>';
        } else {
            $datos = '';
            foreach ($productos as $fila) {
                $datos .= '<tr>';
                $datos .= '<td class="text-center">' . $contador . '</td>';
                $datos .= '<td class="text-center id-producto">' . $fila->id_producto . '</td>';
                $datos .= '<td>' . $fila->descripcion . '</td>';
                $datos .= '<td class="text-right stock">' . $fila->stock . '</td>';
                $datos .= '<td class="resultado text-center"></td>';
                $datos .= '</tr>';
                $contador++;
            }
            return $datos;
        }
    }

    public function stockCeroEmpresa()
    {
        $empresa = 5;
        $query = Producto::select('acuerdo_marco.descripcion AS acuerdo', 'catalogos.descripcion AS catalogo', 'categorias.descripcion AS categoria', 'productos_am.*')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', 'id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', 'id_catalogo')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', 'id_acuerdo_marco')
            ->where('acuerdo_marco.id', 20)
        ->orderByRaw('catalogos.descripcion, categorias.descripcion, productos_am.marca');

        foreach ($query->get() as $key) {
            $stock = StockEmpresaPublicar::where('id_empresa', $empresa)->where('id_producto', $key->id)->first() ?? new StockEmpresaPublicar();
                $stock->id_empresa = $empresa;
                $stock->id_producto = $key->id;
                $stock->stock = 0;
                $stock->publicado = false;
            $stock->save();
        }
        $historial = StockEmpresaPublicar::where('publicado', false)->count();

        die('***FIN DEL PROCESO. TOTAL DE FICHAS A PROCESAR: ' . $historial . '***');
    }
}
