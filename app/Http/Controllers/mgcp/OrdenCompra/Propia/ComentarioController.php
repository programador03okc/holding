<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\OrdenCompra\Propia\Comentario;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\ComentarioOcAm;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\ComentarioOcDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;

class ComentarioController extends Controller
{
    private $nombreFormulario = 'Órdenes de compra propias';

    public function listarPorOc(Request $request)
    {
        if ($request->tipo == 'am') {
            $comentarios = ComentarioOcAm::with('usuario')->where('id_oc', $request->idOc)->orderBy('fecha', 'asc')->get();
            $orden = OrdenCompraAm::find($request->idOc)->orden_am;
        } else {
            $comentarios = ComentarioOcDirecta::with('usuario')->where('id_oc', $request->idOc)->orderBy('fecha', 'asc')->get();
            $orden = OrdenCompraDirecta::find($request->idOc)->nro_orden;
        }

        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 13, null, null, null, 'O/C: ' . $orden);
        return response()->json(array('tipo' => 'success', 'comentarios' => $comentarios), 200);
    }

    public function registrar(Request $request)
    {
        $comentario = $request->tipo == 'am' ? new ComentarioOcAm() : new ComentarioOcDirecta();
            $comentario->id_oc = $request->idOc;
            $comentario->id_usuario = Auth::user()->id;
            $comentario->fecha = new Carbon();
            $comentario->comentario = $request->comentario;
        $comentario->save();

        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $comentario->getTable(), null, $comentario,'O/C: '.($request->tipo == 'am' ? OrdenCompraAm::find($request->idOc)->orden_am : OrdenCompraDirecta::find($request->idOc)->nro_orden));
        return response()->json(array('usuario' => Auth::user()->name, 'fecha' => $comentario->fecha), 200);
    }
}
