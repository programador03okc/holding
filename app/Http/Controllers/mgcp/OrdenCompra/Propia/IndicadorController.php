<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\mgcp\NumberHelper;
use App\Models\mgcp\OrdenCompra\Propia\Indicador;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;

class IndicadorController extends Controller
{
    private $nombreFormulario = 'Config. de indicadores de O/C propias';

    public function configuracion()
    {
        if (!Auth::user()->tieneRol(125)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(),$this->nombreFormulario, 1);
        $indicadores = Indicador::where('id_empresa', Auth::user()->id_empresa)->orderBy('tipo', 'asc')->get();
        return view('mgcp.orden-compra.propia.configuracion-indicadores', get_defined_vars());
    }

    public function actualizarConfiguracion(Request $request)
    {
        if (!Auth::user()->tieneRol(125)) {
            return view('mgcp.usuario.sin_permiso');
        }

        for ($i = 0; $i < count($request->id); $i++) {
            $rojo = str_replace(',', '', $request->rojo[$i]);
            $amarillo = str_replace(',', '', $request->amarillo[$i]);

            if (!is_numeric($rojo) || $rojo <= 0) {
                $request->session()->flash('alert-danger', 'El valor para rojo debe ser un número mayor a 0');
                return redirect()->back();
            }
            if (!is_numeric($amarillo) || $amarillo <= 0) {
                $request->session()->flash('alert-danger', 'El valor para amarillo debe ser un número mayor a 0');
                return redirect()->back();
            }
            if ($amarillo <= $rojo) {
                $request->session()->flash('alert-danger', 'El valor para amarillo debe ser mayor al de rojo');
                return redirect()->back();
            }

            $indicador = Indicador::find($request->id[$i]);
                $indicador->rojo = $rojo;
                $indicador->amarillo = $amarillo;
                $indicador->tipo = $request->tipo[$i];
                $indicador->id_empresa = Auth::user()->id_empresa;
            $indicador->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $indicador->getTable(), json_encode($indicador->getOriginal(), JSON_PRETTY_PRINT),$indicador->toJson(JSON_PRETTY_PRINT));
        }
        $request->session()->flash('alert-success', 'Se ha actualizado la configuración');
        return redirect()->back();
    }

    public function obtenerIndicadorDiario(Request $request)
    {
        $fecha = new Carbon();

        if (!Auth::user()->tieneRol(125)) {
            return response()->json(array(
                'monto' => 'S/ 0.00' ,
                'monto_abreviado' => '0.00', 'estado' => '', 'icono' => '',
                'fecha' => $fecha->format('d-m-Y')
            ), 200);
        }

        $estado = "";
        $icono = "";
        $indicador = Indicador::where('tipo', 1)->where('id_empresa', Auth::user()->id_empresa)->first();
        $monto = OrdenCompraPropiaView::whereRaw("((tipo='am' AND estado_oc='PUBLICADA') OR tipo='directa')")->where('fecha_publicacion', $fecha)->where('id_empresa', Auth::user()->id_empresa)->sum('monto_soles');

        if ($monto <= $indicador->rojo) {
            $estado = "danger";
            $icono = "fa-times";
        } elseif ($monto > $indicador->rojo && $monto <= $indicador->amarillo) {
            $estado = "warning";
            $icono = "fa-exclamation-triangle";
        } else {
            $estado = "success";
            $icono = "fa-check";
        }
        return response()->json(array(
            'monto' => 'S/ ' . number_format($monto, 2),
            'monto_abreviado' => NumberHelper::abreviar($monto), 'estado' => $estado, 'icono' => $icono,
            'fecha' => $fecha->format('d-m-Y')
        ), 200);
    }

    public function obtenerIndicadorMensual(Request $request)
    {
        $fecha = new Carbon();
        if (!Auth::user()->tieneRol(125)) {
            return response()->json(array(
                'monto' => 'S/ 0.00' ,
                'monto_abreviado' => '0.00', 'estado' => '', 'icono' => '',
                'fecha' => $fecha->format('d-m-Y')
            ), 200);
        }
        $estado = "";
        $icono = "";
        $indicador = Indicador::where('tipo', 2)->where('id_empresa', Auth::user()->id_empresa)->first();
        $monto = OrdenCompraPropiaView::obtenerMontoMensual($fecha->month, $fecha->year);

        if ($monto <= $indicador->rojo) {
            $estado = "danger";
            $icono = "fa-times";
        } elseif ($monto > $indicador->rojo && $monto <= $indicador->amarillo) {
            $estado = "warning";
            $icono = "fa-exclamation-triangle";
        } else {
            $estado = "success";
            $icono = "fa-check";
        }
        return response()->json(array(
            'monto' => 'S/ ' . number_format($monto, 2),
            'monto_abreviado' => NumberHelper::abreviar($monto), 'estado' => $estado, 'icono' => $icono,
            'fecha' => $fecha->format('m-Y')
        ), 200);
    }
}
