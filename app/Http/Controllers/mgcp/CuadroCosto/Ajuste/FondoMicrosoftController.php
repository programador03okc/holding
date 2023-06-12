<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Ajuste;

use App\Http\Controllers\Controller;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoMicrosoft;
use App\Models\mgcp\CuadroCosto\Ajuste\MovimientoFondoMicrosoft;
use App\Models\mgcp\CuadroCosto\Ajuste\TipoBolsa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FondoMicrosoftController extends Controller
{
    public function index()
    {
        if (!Auth::user()->tieneRol(66)) {
            return view('mgcp.usuario.sin_permiso');
        }
        return view('mgcp.cuadro-costo.ajuste.fondo-microsoft');
    }

    public function listar(Request $request)
    {
        $html = '';
        $query = TipoBolsa::latest()->get();

        foreach ($query as $row) {
            $movimientos = MovimientoFondoMicrosoft::where('tipo_bolsa_id', $row->id)->orderBy('created_at', 'desc')->get();
            $importe = $row->importe_inicial - $row->importe_consumido;
            $html .= '
            <div class="col-md-6">
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">'. $row->descripcion .'</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool mostrar"><i class="fa fa-plus"></i></button>
                            <!-- <button type="button" class="btn btn-box-tool" data-toggle="tooltip" data-original-title="Contacts"><i class="fa fa-comments"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body box-body-overflow" id="bodyBolsa-'. $row->id .'" style="display: none;">
                        <h3 class="pull-right" style="margin: 0;">Saldo: <strong>'. number_format($importe, 2) .'</strong></h3>
                        <h3 class="box-title">Movimientos</h3>
                        <ul class="nav nav-stacked">';
                        foreach ($movimientos as $key) {
                            switch ($key->tipo_movimiento) {
                                case 1:
                                    $tipo = 'A';
                                    $class = 'text-success';
                                    $descripcion = 'Asignación al '. $key->fondo_microsoft_destino->descripcion;
                                break;
                                case 2:
                                    $tipo = 'T';
                                    $class = 'text-primary';
                                    $descripcion = 'Transferencia de '. $key->fondo_microsoft_origen->descripcion .' al '. $key->fondo_microsoft_destino->descripcion;
                                break;
                                case 3:
                                    $tipo = 'S';
                                    $class = 'text-danger';
                                    $descripcion = 'Salida del '. $key->fondo_microsoft_origen->descripcion .' con CDP '. $key->motivo;
                                break;
                                case 4:
                                    $tipo = 'I';
                                    $class = 'text-success';
                                    $descripcion = 'Incremento en la bolsa '. $key->tipo_bolsa->descripcion;
                                break;
                                case 5:
                                    $tipo = 'C';
                                    $class = 'text-secondary';
                                    $descripcion = 'Creación de '. $key->fondo_microsoft_destino->descripcion;
                                break;
                            }
                            $html .= '
                            <li style="font-size: 16px; padding: 8px;">
                                <span class="badge bg-blue" style="margin-right: 5px; font-size: 15px;">'. $tipo .'</span>'. $descripcion .'
                                <span class="pull-right '. $class .'" style="font-weight: bold;">'. number_format($key->importe, 2) .'</span>';
                            if($key->motivo != null) {
                                $html .= '<small style="display: block; margin-left: 26px;"><strong>Motivo: </strong>'.$key->motivo.'</small>';

                            }
                            $html .= '<small style="display: block; margin-left: 26px;"><strong>Fecha: </strong>'. date('d/m/Y', strtotime($key->fecha)).'</small></li>';
                        }
            $html .= '</ul></div></div></div>';
        }

        return response()->json($html, 200);
    }

    public function listarCombo(Request $request)
    {
        if ($request->tipo == 'bolsa') {
            $data = TipoBolsa::all();
        } else {
            $data = FondoMicrosoft::all();
        }
        return response()->json($data, 200);
    }

    public function registrarBolsa(Request $request)
    {
        try {
            $reg = new TipoBolsa();
                $reg->descripcion = $request->bolsa_descripcion;
                $reg->importe_inicial = ($request->bolsa_importe != '') ? $request->bolsa_importe : 0;
                $reg->importe_consumido = 0;
            $reg->save();
            
            $response = 'ok';
            $alert = 'success';
            $msj = 'Se ha registrado el tipo de bolsa';
            $error = '';
        } catch (Exception $ex) {
            $response = 'null';
            $alert = 'error';
            $msj ='Hubo un problema al ejecutar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'message' => $msj, 'alert' => $alert, 'error' => $error), 200);
    }

    public function registrarFondo(Request $request)
    {
        try {
            $reg = new FondoMicrosoft();
                $reg->descripcion = $request->fondo_descripcion;
                $reg->part_no = $request->fondo_part_no;
                $reg->tipo_bolsa_id = $request->tipo_bolsa_id;
                $reg->importe = $request->fondo_importe;
                $reg->tipo = $request->tipo_fondo;
                $reg->estado = 1;
            $reg->save();

            TipoBolsa::where('id', $request->tipo_bolsa_id)->increment('importe_consumido', $request->fondo_importe);

            $mov = new MovimientoFondoMicrosoft();
                $mov->tipo_movimiento = 5;
                $mov->tipo_bolsa_id = $reg->tipo_bolsa_id;
                $mov->fondo_microsoft_destino_id = $reg->id;
                $mov->motivo = 'Creación de un nuevo fondo';
                $mov->fecha = new Carbon();
                $mov->aprobacion = true;
                $mov->importe = $request->fondo_importe;
            $mov->save();
            
            $response = 'ok';
            $alert = 'success';
            $msj = 'Se ha registrado el fondo microsoft';
            $error = '';
        } catch (Exception $ex) {
            $response = 'null';
            $alert = 'error';
            $msj ='Hubo un problema al ejecutar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'message' => $msj, 'alert' => $alert, 'error' => $error), 200);
    }

    public function registrarMovimiento(Request $request)
    {
        try {
            if ($request->tipo_movimiento != 4) {
                if ($request->tipo_movimiento == 2) {
                    $bolsa = FondoMicrosoft::find($request->fondo_microsoft_origen_id);
                } else {
                    $bolsa = FondoMicrosoft::find($request->fondo_microsoft_destino_id);
                }
                $tipo_bolsa_id = $bolsa->tipo_bolsa_id;
            } else {
                $tipo_bolsa_id = $request->bolsa_id;
            }

            $reg = new MovimientoFondoMicrosoft();
                $reg->tipo_movimiento = $request->tipo_movimiento;
                $reg->tipo_bolsa_id = $tipo_bolsa_id;
                $reg->fondo_microsoft_origen_id = $request->fondo_microsoft_origen_id;
                $reg->fondo_microsoft_destino_id = $request->fondo_microsoft_destino_id;
                $reg->motivo = $request->mov_descripcion;
                $reg->fecha = $request->mov_fecha;
                $reg->aprobacion = true;
                $reg->importe = $request->mov_importe;
            $reg->save();

            
            if ($request->tipo_movimiento == 4) {
                TipoBolsa::where('id', $tipo_bolsa_id)->increment('importe_inicial', $request->mov_importe);
            }
            
            $response = 'ok';
            $alert = 'success';
            $msj = 'Se ha registrado el fondo microsoft';
            $error = '';
        } catch (Exception $ex) {
            $response = 'null';
            $alert = 'error';
            $msj ='Hubo un problema al ejecutar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'message' => $msj, 'alert' => $alert, 'error' => $error), 200);
    }
}
