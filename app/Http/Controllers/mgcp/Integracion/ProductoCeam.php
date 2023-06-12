<?php

namespace App\Http\Controllers\mgcp\Integracion;

use App\Http\Controllers\Controller;
use App\Imports\ProductosCeamImport;
use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
use App\Models\mgcp\Integracion\ProductoCeam as IntegracionProductoCeam;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductoCeam extends Controller
{
    public function index()
    {
        if (!Auth::user()->tieneRol(39)) {
            return view("mgcp.usuario.sin_permiso");
        }
        $catalogos = Catalogo::join("mgcp_acuerdo_marco.acuerdo_marco","id_acuerdo_marco","=","acuerdo_marco.id")->where("activo", true)->where("valido", true)
                    ->orderBy("catalogos.id", "asc")->select(["catalogos.id","catalogos.descripcion AS descripcion_catalogo","acuerdo_marco.descripcion AS descripcion_am"])->get();
        return view("mgcp.integracion.ceam.producto.lista", get_defined_vars());
    }

    public function lista(Request $request)
    {
        $productos = IntegracionProductoCeam::where("activo", true);
        return datatables($productos)->toJson();
    }

    public function descargarPlantilla()
    {
        return Storage::download("mgcp/plantillas/plantilla-productos-ceam.xlsx");
    }

    public function importar(Request $request)
    {
        try {
            set_time_limit(6000);
            $file = $request->file("archivo");
            $import = new ProductosCeamImport;
            Excel::import($import, $file);
            
            $response = "ok";
            $alert = "success";
            $msj = "Se ha importado ".$import->getRowCount(1)." nuevos productos y se detectaron ".$import->getRowCount(2)." productos duplicados";
            $error = "";
        } catch (Exception $ex) {
            $response = "error";
            $alert = "danger";
            $msj ="Hubo un problema al importar. Por favor intente de nuevo";
            $error = $ex;
        }
        return response()->json(array("response" => $response, "alert" => $alert, "message" => $msj, "error" => $error), 200);
    }
}
