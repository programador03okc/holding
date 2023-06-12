<?php


namespace App\Helpers\mgcp\CuadroCosto;

use App\Mail\mgcp\CuadroCosto\AprobacionCuadro;
use App\Mail\mgcp\CuadroCosto\RespuestaSolicitud;
use App\Mail\mgcp\CuadroCosto\RetiroAprobacionCuadro;
use App\Mail\mgcp\CuadroCosto\SolicitudAprobacion;
use App\Models\Almacen\Requerimiento;
use App\Models\mgcp\CuadroCosto\AprobadorUno;
use App\Models\mgcp\CuadroCosto\AprobadorDos;
use App\Models\mgcp\CuadroCosto\AprobadorTres;
use App\Models\mgcp\Usuario\Notificacion;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Exception;

class SolicitudHelper
{
    public static function obtenerAprobadoresPorMonto($detalles)
    {
        $pvt = $detalles->moneda == '$' ? $detalles->precio_venta_total * $detalles->tipo_cambio : $detalles->precio_venta_total; //La tasa de aprobación está en soles
        $aprobadores = array();
        $aprobadorUno = AprobadorUno::where('valor_venta', '>=', $pvt)->where('margen_minimo', '<=', ceil($detalles->margen_ganancia))->orderBy('valor_venta', 'asc')->first();
        if ($aprobadorUno != null) {
            $aprobadores[] = $aprobadorUno->id_usuario;
            return $aprobadores;
        }
        $aprobadorTres = AprobadorTres::first();
        $aprobadores[] = $aprobadorTres->id_usuario;
        return $aprobadores;
    }

    public static function enviarSolicitudAprobacion($codTipoSolicitud, $usuario, $oportunidad, $detalles, $comentario)
    {
        $tipoSolicitud = '';
        $asuntoCorreo = '';
        $destinatario = User::find($usuario);
        switch ($codTipoSolicitud) {
            case 1:
                $tipoSolicitud = 'la aprobación';
                $asuntoCorreo = 'aprobación';
                break;
            case 2:
                $tipoSolicitud = 'el retiro de aprobación';
                $asuntoCorreo = 'retiro de aprobación';
                break;
            case 3:
                $tipoSolicitud = 'la reapertura';
                $asuntoCorreo = 'reapertura';
                break;
            case 4:
                $tipoSolicitud = 'la aprobación pendiente de regularización';
                $asuntoCorreo = 'aprobación pendiente de regularización';
                break;
        }

        $notificacion = new Notificacion;
            $notificacion->id_usuario = $usuario;
            $notificacion->mensaje = '<strong>' . Auth::user()->name . '</strong> ha solicitado ' . $tipoSolicitud . ' del cuadro de costo ' .
                $oportunidad->codigo_oportunidad . (empty($comentario) ? '' : '. Comentario del usuario: ' . $comentario) .
                '. Oportunidad: ' . $oportunidad->oportunidad
                . '. Cliente: ' . $oportunidad->entidad->nombre
                . '. Costo total: ' . $detalles->costo_total_format
                . '. Precio de venta total: ' . $detalles->precio_venta_total_format
                . '. Ganancia: <strong> ' . $detalles->ganancia_real_format . '</strong>'
                . '. Margen de ganancia: ' . $detalles->margen_ganancia_format;
            $notificacion->fecha = new Carbon;
            $notificacion->url = route('mgcp.cuadro-costos.detalles', ['id' => $detalles->id_oportunidad]);
            $notificacion->leido = 0;
        $notificacion->save();
        // Mail::to((config('app.debug') ? config('global.adminEmail') : $destinatario->email))->send(new SolicitudAprobacion($tipoSolicitud, $detalles, $comentario, $notificacion->url, $oportunidad, Auth::user(), $asuntoCorreo));
    }

    public static function enviarRespuestaSolicitud($cuadro, $solicitud)
    {
        $url = route('mgcp.cuadro-costos.detalles', ['id' => $cuadro->id_oportunidad]);
        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
        $autorSolicitud = User::find($solicitud->enviada_por);
        $tipoSolicitud = '';
        switch ($solicitud->id_tipo) {
            case 1:
                $tipoSolicitud = 'aprobación';
                break;
            case 2:
                $tipoSolicitud = 'retiro de aprobación';
                break;
        }
        // Mail::to((config('app.debug') ? config('global.adminEmail') : $autorSolicitud->email))->send(new RespuestaSolicitud($solicitud, $url, $oportunidad, Auth::user(), $tipoSolicitud));
    }
}
