<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\EmpresaAcuerdo;
use Illuminate\Http\Request;

class PeruComprasController extends Controller
{

    /**
     * Obtiene los acuerdos registrados en la BD local de la empresa seleccionada
     */
    public function obtenerAcuerdosLocal(Request $request) {
        $resultado = EmpresaAcuerdo::join('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', 'id_acuerdo_marco')
            ->where('id_empresa', $request->idEmpresa)->where('acuerdo_marco.activo', true)->where('acuerdo_marco.valido', true)
            ->select(['acuerdo_marco.id_pc AS id','acuerdo_marco.descripcion'])->get();

        if ($resultado->count() == 0) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Sin acuerdos para la empresa seleccionada. Operación no puede continuar'), 200);
        } else {
            return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'data' => $resultado), 200);
        }
    }

    public function obtenerAcuerdos(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) { // ealvarez 3
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'No se pudo iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo'), 200);
        } else {

            $url = "";
            //Determinar la URL
            switch ($request->pagina) {
                case 'productos':
                    $url = 'https://www.catalogos.perucompras.gob.pe/Reportes/ProductoOfertadoIndex';
                    //$idSelectAcuerdos="ajaxAcuerdo";
                    break;
                case 'mejorar_plazo':
                    $url = 'https://www.catalogos.perucompras.gob.pe/MejoraPlazo/obtenerFiltros';
                    break;
                case 'proformas':
                    $url = 'https://www.catalogos.perucompras.gob.pe/t_Proforma/obtenerFiltros';
                    //$idSelectAcuerdos="cboAcuerdo";
                    break;
                case 'productos_nuevo_acuerdo':
                    $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertado';
                    //$idSelectAcuerdos="ajaxAcuerdo";
                    break;
                case 'productos_acuerdo_vigente':
                    $url = 'https://www.catalogos.perucompras.gob.pe/t_ProductoOfertadoAmp';
                    //$idSelectAcuerdos="";
                    break;
            }
            //Pasos adicionales dependiendo de la página de donde se obtendrán los acuerdos
            $acuerdos = [];
            $filas = '';
            switch ($request->pagina) {
                case 'proformas':
                    $data = $portal->visitarUrl($url);
                    $filas = explode("¬", substr($data, 0, strpos($data, "¯")));
                    foreach ($filas as $fila) {
                        $data = explode("^", $fila);
                        if (strpos($data[1], "No Vigente") == false) {
                            $acuerdo = new \stdClass();
                                $acuerdo->id = $data[0];
                                $acuerdo->descripcion = $data[1];
                            array_push($acuerdos, $acuerdo);
                        }
                    }
                break;
                case 'mejorar_plazo':
                    $lista = explode("¬", $portal->enviarData('0^' . $empresa->id_pc, $url));
                    foreach ($lista as $fila) {
                        $data = explode("^", $fila);
                        $acuerdo = new \stdClass();
                            $acuerdo->id = $data[0];
                            $acuerdo->descripcion = $data[1];
                        array_push($acuerdos, $acuerdo);
                    }
                break;
                default:
                    $pagina = $portal->parseHtml($portal->visitarUrl($url));
                    foreach ($pagina->find("select[id=ajaxAcuerdo] option") as $opcion) {
                        if ($opcion->value == 0) {
                            continue;
                        }
                        $acuerdo = new \stdClass(); //Acuerdo Marco con ID de Perú Compras
                        $acuerdo->descripcion = $opcion->innertext; //strstr($opcion->innertext, ' ', true);
                        $acuerdo->id = $opcion->value;
                        $acuerdos[] = $acuerdo;
                    }
                break;
            }
            //$portal->finalizarCurl();
            if (count($acuerdos) == 0) {
                return response()->json(array('tipo' => 'danger', 'mensaje' => 'Sin acuerdos para la empresa seleccionada. Operación no puede continuar'), 200);
            } else {
                return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'data' => $acuerdos), 200);
            }
        }
    }

    public function obtenerCatalogos(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3);
        $dataEnviar = array();
        $dataEnviar['N_Acuerdo'] = $request->idAcuerdo;
        $dataEnviar['C_Estado'] = 'ACTIVO';
        $dataEnviar['__RequestVerificationToken'] = $portal->token;
        $resultado = $portal->enviarData($dataEnviar, 'https://www.catalogos.perucompras.gob.pe/General/ListaJ_CatalogoAcuerdo');
        $catalogos = json_decode($resultado);
        //$portal->finalizarCurl();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'data' => $catalogos), 200);
    }

    public function obtenerCategorias(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3);
        $dataEnviar = array();
        $dataEnviar['N_Catalogo'] = $request->idCatalogo;
        $dataEnviar['N_CategoriaParent'] = '0';
        $dataEnviar['C_Estado'] = 'ACTIVO';
        $dataEnviar['N_Nivel'] = '1';
        $dataEnviar['__RequestVerificationToken'] = $portal->token;
        $resultado = $portal->enviarData($dataEnviar, 'https://www.catalogos.perucompras.gob.pe/General/ListaJ_CategoriaCatalogo');
        $catalogos = json_decode($resultado);
        //$portal->finalizarCurl();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'data' => $catalogos), 200);
    }

    public function obtenerProvincias(Request $request)
    {
        $empresa = Empresa::find($request->idEmpresa);
        $portal = new PeruComprasHelper();
        if (!$portal->login($empresa, 3)) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Error al iniciar sesión'), 200);
        }
        $lista = explode("¬", $portal->enviarData('4^' . $request->idDepartamento, 'https://www.catalogos.perucompras.gob.pe/MejoraPlazo/obtenerFiltros'));
        $provincias = [];
        foreach ($lista as $fila) {
            $data = explode("^", $fila);
            $provincia = new \stdClass();
            $provincia->id = $data[0];
            $provincia->nombre = $data[1];
            array_push($provincias, $provincia);
        }
        return response()->json(array('tipo' => 'success', 'mensaje' => 'ok', 'data' => $provincias), 200);
    }
}
