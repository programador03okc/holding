<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Ajuste;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoIngreso;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedor;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedorView;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoUtilizadoView;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FondoProveedorController extends Controller
{
    private $nombreFormulario = 'Fondos de proveedores';

    public function index()
    {
        if (!Auth::user()->tieneRol(127)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.cuadro-costo.ajuste.fondos-proveedores');
    }

    public function dataLista(Request $request)
    {
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables(FondoProveedorView::select(['*']))->toJson();
    }

    public function dataListaParaProformas(Request $request)
    {
        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables(FondoProveedorView::where('activo', true)->where('cantidad_disponible', '>', 0)->select(['*']))->toJson();
    }

    public function registrarFondo(Request $request)
    {
        if (!Auth::user()->tieneRol(127)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No tiene permiso para registrar el fondo'), 200);
        }

        if (!is_numeric($request->valor) || $request->valor <= 0) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El valor debe ser un número mayor a 0'), 200);
        }
        if (!is_numeric($request->cantidad_inicial) || $request->cantidad_inicial <= 0) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'La cantidad inicial debe ser un número mayor a 0'), 200);
        }
        DB::beginTransaction();
        try {
            $fondo = new FondoProveedor();
                $fondo->descripcion = $request->descripcion;
                $fondo->moneda = $request->moneda;
                $fondo->valor_unitario = $request->valor;
                $fondo->activo = true;
            $fondo->save();
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 4, $fondo->getTable(), null, $fondo);

            $ingreso = new FondoIngreso();
                $ingreso->id_fondo_proveedor = $fondo->id;
                $ingreso->cantidad = $request->cantidad_inicial;
                $ingreso->fecha = new Carbon();
                $ingreso->id_usuario = Auth::user()->id;
            $ingreso->save();
            LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 4, $ingreso->getTable(), null, $ingreso);

            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha registrado el fondo'), 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al registrar. Por favor actualice la página e intente de nuevo'), 200);
        }
    }

    public function cambiarEstado(Request $request)
    {
        $fondo = FondoProveedor::find($request->idFondo);
            $dataAnterior['activo'] = $fondo->activo;
            $fondo->activo = !$fondo->activo;
            $dataNueva['activo'] = $fondo->activo;
        $fondo->save();
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 2, $fondo->getTable(), $dataAnterior, $dataNueva,'ID fondo: '.$fondo->id.', fondo: '.$fondo->descripcion);
        return response()->json(array('tipo' => 'success', 'mensaje' => 'El fondo ha sido ' . ($fondo->activo ? 'reactivado' : 'desactivado')), 200);
    }

    public function obtenerFondosDisponibles(Request $request)
    {
        $fondos = FondoProveedorView::where('cantidad_disponible', '>', 0)->where('activo', true)->orderBy('descripcion')->get();
        return response()->json($fondos);
    }

    public function listarIngresosFondo(Request $request)
    {
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 9, null, null, null, 'Ingresos de fondo: ' . FondoProveedor::find($request->id_fondo_proveedor)->descripcion);
        return datatables(FondoIngreso::join('mgcp_usuarios.users', 'id_usuario', 'users.id')
            ->where('id_fondo_proveedor', $request->id_fondo_proveedor)->select(['cantidad', 'fecha', 'users.name']))->toJson();
    }

    public function listarUtilizadosFondo(Request $request)
    {
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 9, null, null, null, 'Usos de fondo: ' . FondoProveedor::find($request->id_fondo_proveedor)->descripcion);
        return datatables(FondoUtilizadoView::where('id_fondo_proveedor', $request->id_fondo_proveedor)->select(['*']))->toJson();
    }

    public function registrarIngreso(Request $request)
    {
        if (!Auth::user()->tieneRol(127)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No tiene permiso para registrar el fondo'), 200);
        }

        if (!is_numeric($request->cantidad) || $request->cantidad <= 0) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'La cantidad ser un número mayor a 0'), 200);
        }
        $fondo = new FondoIngreso();
            $fondo->id_fondo_proveedor = $request->idFondoProveedor;
            $fondo->cantidad = $request->cantidad;
            $fondo->fecha = new Carbon();
            $fondo->id_usuario = Auth::user()->id;
        $fondo->save();
        LogActividad::registrar(Auth::user(),  $this->nombreFormulario, 4, $fondo->getTable(), null, $fondo);
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha registrado el ingreso'), 200);
    }
}
