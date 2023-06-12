<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\Contribuyente;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Despacho;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DespachoController extends Controller
{
    private $nombreFormulario = 'Despacho de O/C propias';

    public function obtenerDetalles(Request $request)
    {
        $ordenCompra = $request->tipo == 'directa' ? OrdenCompraDirecta::find($request->id) : OrdenCompraAm::find($request->id);
        $nroOrden = $request->tipo == 'directa' ? $ordenCompra->nro_orden : $ordenCompra->orden_am;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 9, null, null, null, 'O/C: ' . $nroOrden);
        return response()->json(Despacho::find($ordenCompra->id_despacho));
    }

    public function actualizar(Request $request)
    {
        if (!Auth::user()->tieneRol(51)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Sin permiso para realizar el cambio'));
        }
        if ($request->despachada == 1 && empty($request->fechaSalida)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Debe ingresar una fecha de salida'));
        }

        DB::beginTransaction();
        try {
            $ordenCompra = $request->tipo == 'directa' ? OrdenCompraDirecta::find($request->id) : OrdenCompraAm::find($request->id);
            $nroOrden = $request->tipo == 'directa' ? $ordenCompra->nro_orden : $ordenCompra->orden_am;
            $despacho = Despacho::find($ordenCompra->id_despacho);

            if ($request->despachada == 0) {
                Despacho::destroy($ordenCompra->id_despacho);
                    $ordenCompra->id_despacho = null;
                $ordenCompra->save();

                if (!is_null($despacho)) {
                    LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $despacho->getTable(), $despacho, null, 'O/C: ' . $ordenCompra);
                    $despacho->delete();
                }
            } else {
                if ($despacho == null) {
                    $existe = false;
                    $despacho = new Despacho();
                } else {
                    $existe = true;
                }
                
                    $despacho->id_transportista = $request->transportista == 0 ? null : $request->transportista;
                    $despacho->flete_real = $request->fleteReal;
                    $despacho->fecha_salida = $request->fechaSalida;
                    $despacho->fecha_llegada = $request->fechaLlegada;
                    $despacho->id_usuario = Auth::user()->id;
                    $despacho->fecha_registro = new Carbon();
                $despacho->save();
                    
                    $ordenCompra->id_despacho = $despacho->id;
                $ordenCompra->save();

                $comentario = 'O/C: ' . $nroOrden . ', transportista: ' . ($request->transportista == 0 ? '' : Contribuyente::find($request->transportista)->razon_social);
                if ($existe) {
                    $original = [];
                    $cambios = [];

                    foreach ($despacho->attributesToArray() as $key => $value) {
                        if ($despacho->getOriginal()[$key] != $value) {
                            if ($key == 'id_transportista') {
                                $original[$key] = $despacho->getOriginal()[$key] == null ? '' : Contribuyente::find($despacho->getOriginal()[$key])->razon_social;
                                $cambios[$key] = $value == null ? '' : Contribuyente::find($value)->razon_social;
                            } else {
                                $original[$key] = $despacho->getOriginal()[$key];
                                $cambios[$key] = $value;
                            }
                        }
                    }
                    LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $despacho->getTable(), $original, $cambios, $comentario);
                } else {
                    LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $despacho->getTable(), null, $despacho, $comentario);
                }
            }
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha actualizado el despacho', 'despachada' => $request->despachada), 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al actualizar el despacho: '.$ex->getMessage()), 200);
        }
    }
}
