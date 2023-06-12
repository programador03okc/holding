<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Entidad;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Entidad\Contacto;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactoEntidadController extends Controller
{

    public function listar(Request $request)
    {
        $contactos = Contacto::where('id_entidad', $request->idEntidad)->orderBy('nombre', 'asc')->get();
        $orden = $request->tipoOrden == 'am' ? OrdenCompraAm::find($request->idOrdenCompra) : OrdenCompraDirecta::find($request->idOrdenCompra);
        $seleccionado = $request->idOrdenCompra == null ? null : $orden->id_contacto;
        return response()->json(array('tipo' => 'success', 'contactos' => $contactos, 'seleccionado' => $seleccionado));
    }

    public function eliminar(Request $request)
    {
        if (!Auth::user()->tieneRol(60)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para eliminar contactos'));
        }
        if ($request->tipoOrden == 'am') {
            OrdenCompraAm::where('id_contacto', $request->idContacto)->update(['id_contacto' => null]);
        } else {
            OrdenCompraDirecta::where('id_contacto', $request->idContacto)->update(['id_contacto' => null]);
        }
        Contacto::destroy($request->idContacto);
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminar el contacto'));
    }

    public function obtenerDetalles(Request $request)
    {
        return response()->json(array('tipo' => 'success', 'contacto' => Contacto::find($request->idContacto)));
    }

    public function actualizar(Request $request)
    {
        if (!Auth::user()->tieneRol(60)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para actualizar contactos'));
        }
        $rules = array(
            'idContacto' => 'required',
            'nombre' => 'required|max:100',
            'telefono' => 'required|max:50',
            'email' => 'max:100',
            'cargo' => 'max:50',
            'direccion' => 'max:255',
            'horario' => 'max:255',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar. Por favor intente de nuevo'));
        } else {
            $contacto = Contacto::find($request->idContacto);
            $contacto->nombre = $request->nombre;
            $contacto->telefono = $request->telefono;
            $contacto->email = $request->correo;
            $contacto->cargo = $request->cargo;
            $contacto->direccion = $request->direccion;
            $contacto->horario = $request->horario;
            $contacto->fecha_registro = new Carbon();
            $contacto->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha actualizado el contacto'));
        }
    }

    public function agregar(Request $request)
    {
        if (!Auth::user()->tieneRol(60)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para registrar contactos'));
        }
        $rules = array(
            'idEntidad' => 'required',
            'nombre' => 'required|max:100',
            'telefono' => 'required|max:50',
            'email' => 'max:100',
            'cargo' => 'max:50',
            'direccion' => 'max:255',
            'horario' => 'max:255',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al registrar. Por favor intente de nuevo'));
        } else {
            $contacto = new Contacto();
            $contacto->id_entidad = $request->idEntidad;
            $contacto->nombre = $request->nombre;
            $contacto->telefono = $request->telefono;
            $contacto->email = $request->correo;
            $contacto->cargo = $request->cargo;
            $contacto->direccion = $request->direccion;
            $contacto->horario = $request->horario;
            $contacto->fecha_registro = new Carbon();
            $contacto->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha agregado el contacto','contacto'=>$contacto));
        }
    }
}
