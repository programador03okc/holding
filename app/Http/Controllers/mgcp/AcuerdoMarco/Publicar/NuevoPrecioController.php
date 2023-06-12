<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Publicar;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\PublicarProducto;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NuevoPrecioController extends Controller
{
    private $nombreFormulario = 'Publicar nuevos precios';

    public function index()
    {
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $empresas = Empresa::where('id', Auth::user()->id_empresa)->get();
        return view('mgcp.acuerdo-marco.publicar.nuevos-precios', get_defined_vars());
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
                $existe = PublicarProducto::where('id_empresa', $request->empresa)->where('id_producto', $row[0])->where('tipo', 0)->first();
                if ($existe == null) {
                    $registrar = new PublicarProducto();
                    $registrar->id_empresa = $request->empresa;
                    $registrar->id_producto = $row[0];
                    $registrar->tipo = 0;
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
        $publicar = PublicarProducto::find($request->id);
        $producto = Producto::find($publicar->id_producto);
        $campoId = 'id_' . $empresa->nombre_corto;
        $campoPrecio='precio_'. $empresa->nombre_corto;
        $categoria=Categoria::find($producto->id_categoria);
        $catalogo=Catalogo::find($categoria->id_catalogo);
        $acuerdo=AcuerdoMarco::find($catalogo->id_acuerdo_marco);

        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 2
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Problema al iniciar sesión'), 200);
        }
        $dataEnviar = array();
        $dataEnviar['ID_ProductoOfertado'] = $producto->$campoId;
        $dataEnviar['N_Acuerdo'] = $acuerdo->id_pc;
        $dataEnviar['N_Catalogo'] = $catalogo->id_pc;
        $dataEnviar['N_Categoria'] = $categoria->id_pc;

        $dataEnviar['N_PrecioOfertadoAnt'] = str_replace(['$','S/',' ',','],'',$producto->$campoPrecio);
        $dataEnviar['N_PrecioOfertado'] = $publicar->precio;
        $url = "https://www.catalogos.perucompras.gob.pe/MejoraBasica/ReducirPrecio";
        $dataEnviar['__RequestVerificationToken'] = $portal->token;

        $mensaje = $portal->parseHtml($portal->enviarData($dataEnviar, $url))->find('div[id=MensajeModal] div.modal-body', 0)->plaintext;
        if (strpos($mensaje, 'satisfactoria') !== false) {
            $publicar->publicado=true;
            $publicar->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Procesado'), 200);
        }
        else
        {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Problema al enviar'), 200);
        }
    }

    public function obtenerProductos(Request $request)
    {
        $productos = PublicarProducto::join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->where('id_empresa', $request->idEmpresa)->where('tipo', 0)
            ->where('publicado', false)
            ->select('publicar_precios.id', 'productos_am.id_pc', 'productos_am.descripcion', 'precio', 'moneda')
            ->orderBy('publicar_precios.id')->get();
        $contador = 1;
        $datos = '';
        foreach ($productos as $producto) {
            $datos .= '<tr>';
            $datos .= '<td class="text-center">' . $contador . '</td>';
            $datos .= '<td class="text-center id">' . $producto->id . '</td>';
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
