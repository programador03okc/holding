<?php

namespace App\Http\Controllers\mgcp\CuadroCosto\Ajuste;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\TcSbs;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\Usuario\LogActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoCambioController extends Controller
{
    private $nombreFormulario='Tipo de cambio para cuadros de presupuesto';

    public function index()
    {
        if (!Auth::user()->tieneRol(126)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $tipoCambio = TipoCambio::find(1);
        return view('mgcp.cuadro-costo.ajuste.tipo-cambio')->with(compact('tipoCambio'));
    }

    public function actualizar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_cambio' => 'required|numeric',
        ]);

        if (!Auth::user()->tieneRol(126)) {
            $request->session()->flash('alert-danger', 'Usuario sin permiso para actualizar el tipo de cambio');
        } else {
            if ($validator->fails()) {
                $request->session()->flash('alert-danger', 'El tipo de cambio debe ser un número');
            } else {
                $tipo = TipoCambio::find(1);
                $tipo->tipo_cambio = $request->tipo_cambio;
                $tipo->save();
                LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2,$tipo->getTable(),$tipo->getOriginal(),$tipo);
                $request->session()->flash('alert-success', 'Se ha actualizado el tipo de cambio');
            }
        }
        return redirect()->route('mgcp.cuadro-costos.ajustes.tipo-cambio.index');
    }

    public function obtenerTipoCambioSbs(Request $request)
    {
        $fecha = Carbon::parse($request->fecha)->format("Y-m-d");
        $url = 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha='.$fecha;
        $apiQ = json_decode($this->consultApiSunat($url));
        $data = $apiQ->compra;
        return response()->json($data, 200);
    }

    public function consultApiSunat($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        return curl_exec($curl);
    }
}
