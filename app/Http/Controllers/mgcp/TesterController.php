<?php

namespace App\Http\Controllers\mgcp;

use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

set_time_limit(0);

class TesterController extends Controller
{
    public function oportunidades()
    {
        $total = 0; $modif = 0;
        $oport = Oportunidad::whereNull('id_empresa')->get();

        foreach ($oport as $key) {
            $orden = OrdenCompraPropiaView::where('id_oportunidad', $key->id)->first();

            if ($orden) {
                $actual = Oportunidad::find($key->id);
                    $actual->id_empresa = $orden->id_empresa;
                $actual->save();
                $modif++;
            }
            $total++;
        }

        return response()->json(array('Total de oportunidades' => $total, 'Oport actualizadas' => $modif), 200);
    }

    public function codigoOportunidades()
    {
        $total = 0;
        $oport = Oportunidad::get();
        
        foreach ($oport as $key) {
            $abrev = '';
            $codigo = $key->codigo_oportunidad;
            
            if ($key->id_empresa != null) {
            //     $abrev = Empresa::find($key->id_empresa)->abreviado;
            // $nuevoCodigo = $abrev.$this->substrCodigo($codigo);
                $nuevoCodigo = $this->substrCodigo($codigo);
                $actual = Oportunidad::find($key->id);
                    $actual->codigo_oportunidad = $nuevoCodigo;
                $actual->save();
                $total++;
            }
        }
        return response()->json(array('Total de oportunidades' => $total), 200);
    }

    public function substrCodigo($codigo)
    {
        // $midato = str_replace('OKC', '', $codigo);
        $extract = Str::substr($codigo, 0, 3);
        $midato = str_replace($extract, '', $codigo);
        return $midato;
    }

}
