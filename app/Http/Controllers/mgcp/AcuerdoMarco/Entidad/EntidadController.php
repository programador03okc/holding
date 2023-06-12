<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Entidad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Helpers\mgcp\EntidadHelper;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EntidadController extends Controller
{
    private $nombreFormulario = 'Entidades';

    public function lista()
    {
        /*if (!Auth::user()->tieneRol(39)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $catalogos = Catalogo::whereRaw('id_acuerdo_marco IN (SELECT id FROM mgcp_acuerdo_marco.acuerdo_marco WHERE activo=true)')->orderBy('id', 'asc')->get();*/
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.entidad.lista');
    }

    public function dataLista(Request $request)
    {
        $entidades = Entidad::select(['*']);
        return datatables($entidades)->rawColumns(['semaforo'])->toJson();
    }

    public function registrar(Request $request)
    {
        $rules = array(
            'ruc' => 'required|max:11|min:8',
            'nombre' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => $validator->errors()->first()), 200);
        }
        if ($request->ruc != null && EntidadHelper::existeRuc($request->ruc)) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'El RUC ya ha sido registrado anteriormente. Ingrese otro RUC e intente de nuevo.'), 200);
        }

        if (EntidadHelper::existeNombre($request->nombre)) {
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'La entidad ya ha sido registrada anteriormente. Ingrese otra entidad e intente de nuevo.'), 200);
        }

        $entidad = new Entidad();
            $entidad->ruc = $request->ruc;
            $entidad->nombre = mb_strtoupper($request->nombre);
            $entidad->ubigeo = mb_strtoupper($request->ubigeo);
            $entidad->telefono = $request->telefono;
            $entidad->direccion = mb_strtoupper($request->direccion);
        $entidad->save();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha registrado la entidad ' . $entidad->nombre, 'id' => $entidad->id, 'nombre' => $entidad->nombre), 200);
    }

    public function detalles(Request $request)
    {
        $entidad = Entidad::find($request->id);
        return response()->json($entidad, 200);
    }

    public function buscarRuc(Request $request)
    {
        if (!EntidadHelper::existeRuc($request->ruc)) {
            return response()->json(array('tipo' => 'success', 'mensaje' => 'No existe'));
        } else {
            return response()->json(array('tipo' => 'danger', 'mensaje' => "El DNI/RUC $request->ruc  ya existe"));
        }
    }

    public function buscarNombre(Request $request)
    {
        $nombre = mb_strtoupper($request->nombre);
        if (!EntidadHelper::existeNombre($nombre)) {
            return response()->json(array('tipo' => 'success', 'mensaje' => 'No existe'));
        } else {
            return response()->json(array('tipo' => 'danger', 'mensaje' => "Ya existe el cliente / entidad con nombre $nombre"));
        }
    }

    public function buscarEntidad(Request $request)
    {
        $busqueda = ($request->has('q')) ? $request->input('q') : '';
        $clientes = Entidad::whereRaw('lower(nombre) like ?', ['%' . strtolower($busqueda) . '%'])->get();
        $dataTotal = array();
        foreach ($clientes as $cliente) {
            $dataTotal[] = [$cliente->id, $cliente->ruc, $cliente->nombre];
        }
        $data[] = array(
            'TotalRows' => count($dataTotal),
            'Rows' => $dataTotal
        );
        return response()->json($data);
    }

    public function actualizar(Request $request)
    {
        if (EntidadHelper::existeRuc($request->ruc, $request->id)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El DNI/RUC ingresado ya existe. Por favor ingrese otro DNI/RUC y vuelva a intentarlo'), 200);
        }
        if (EntidadHelper::existeNombre($request->nombre, $request->id)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El nombre de la entidad ya ha sido registrada anteriormente. Por favor ingrese otro nombre y vuelva a intentarlo'), 200);
        }
        $entidad = Entidad::find($request->id);
            $entidad->ruc = $request->ruc;
            $entidad->nombre = $request->nombre;
            $entidad->direccion = $request->direccion;
            $entidad->ubigeo = $request->ubigeo;
            $entidad->responsable = $request->responsable;
            $entidad->telefono = $request->telefono;
            $entidad->cargo = $request->cargo;
            $entidad->correo = $request->correo;
            $entidad->nombre_contacto = $request->nombre_contacto;
            $entidad->telefono_contacto = $request->telefono_contacto;
            $entidad->correo_contacto = $request->correo_contacto;
            $entidad->cargo_contacto = $request->cargo_contacto;
            $entidad->comentario = $request->comentario;
        $entidad->save();
        return response()->json(array('tipo' => 'success', 'mensaje' => 'La entidad ha sido actualizada'), 200);
    }

    public function actualizarCampo(Request $request)
    {
        $campo = $request->campo;
        $data = Entidad::find($request->id);
        if ($campo == 'correo') {
            $data->$campo = mb_strtolower($request->valor);
        } else {
            $data->$campo = mb_strtoupper($request->valor);
        }
        $data->save();
        return response()->json(true, 200);
    }
}
