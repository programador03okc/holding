<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Ajuste;

use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Licencia;
use App\Models\mgcp\Usuario\LogActividad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenciaController extends Controller
{
    private $nombreFormulario = 'Licencias para cuadros de presupuesto';

    public function index()
    {
        if (!Auth::user()->tieneRol(65)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 1);
        return view('mgcp.cuadro-costo.ajuste.licencia');
    }

    public function listar(Request $request)
    {
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 7, null, null, null,'Criterio: ' . $request->search['value']);
        }
        $data = Licencia::where('id_empresa', Auth::user()->id_empresa);
        return datatables($data)->toJson();
    }

    public function guardar(Request $request)
    {
        try {
            $data = new Licencia();
                $data->marca = $request->marca;
                $data->part_no = $request->part_no;
                $data->descripcion = $request->descripcion;
                $data->id_empresa = Auth::user()->id_empresa;
            $data->save();
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 4, $data->getTable(), null, $data);

            $response = 'ok';
            $alert = 'success';
            $error = '';
            if ($request->id > 0) {
                $message = 'Se ha editado la licencia';
            } else {
                $message = 'Se ha registrado la licencia';
            }
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'error';
            $message = 'Hubo un problema al registrar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $message, 'error' => $error), 200);
    }
}
