<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Producto\HistorialActualizacion;
use App\Models\mgcp\Usuario\LogActividad;

class HistorialActualizacionController extends Controller 
{
    private $nombreFormulario = 'Historial de actualizaciones de producto';

    public function lista() {
        if (!Auth::user()->tieneRol(39)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 1);
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        return view('mgcp.acuerdo-marco.producto.historial-actualizaciones')->with(compact('empresas'));
    }

    public function obtenerHistorialProducto(Request $request) {
        // if ($request->idEmpresa == 0) {
        //     $historial = HistorialActualizacion::with('usuario', 'empresa', 'producto')->where('id_producto', $request->idProducto)->orderBy('fecha', 'DESC')->get();
        // } else {
        //     $historial = HistorialActualizacion::with('usuario', 'empresa', 'producto')->where('id_producto', $request->idProducto)->where('id_empresa', $request->idEmpresa)->orderBy('fecha', 'DESC')->get();
        // }
        $historial = HistorialActualizacion::with('usuario', 'empresa', 'producto')->where('id_producto', $request->idProducto)->where('id_empresa', Auth::user()->idEmpresa)->orderBy('fecha', 'DESC')->get();
        return response()->json($historial, 200);
    }

    public function dataLista(Request $request) 
    {
        // $historial = HistorialActualizacion::join('mgcp_acuerdo_marco.productos_am', 'id_producto', '=', 'productos_am.id')
        //         ->join('mgcp_usuarios.users', 'id_usuario', '=', 'users.id')
        //         ->join('mgcp_acuerdo_marco.empresas', 'id_empresa', '=', 'empresas.id')
        //         ->select(['name', 'empresa', 'productos_am.id', 'marca', 'modelo', 'part_no', 'detalle', 'comentario', 'fecha', 'imagen', 'ficha_tecnica']);
        $historial = HistorialActualizacion::join('mgcp_acuerdo_marco.productos_am', 'producto_historial_actualizaciones.id_producto', '=', 'productos_am.id')
                ->join('mgcp_usuarios.users', 'producto_historial_actualizaciones.id_usuario', '=', 'users.id')
                ->join('mgcp_acuerdo_marco.empresas', 'producto_historial_actualizaciones.id_empresa', '=', 'empresas.id')
                ->select(['name', 'empresa', 'productos_am.id', 'marca', 'modelo', 'part_no', 'detalle', 'comentario', 'fecha', 'imagen', 'ficha_tecnica'])
                ->where('producto_historial_actualizaciones.id_empresa', Auth::user()->id_empresa);
        
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables($historial)->toJson();
    }

}
