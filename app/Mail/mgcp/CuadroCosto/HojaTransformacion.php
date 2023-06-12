<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HojaTransformacion extends Mailable
{
    use Queueable, SerializesModels;

    public $oportunidad;

    public function __construct($oportunidad)
    {
        $this->oportunidad = $oportunidad;
    }
    
    public function build()
    {
        $orden = $this->oportunidad->ordenCompraPropia;
        $asunto = [];
        $asunto[] = 'O. SERVICIO';
        if ($orden == null) {
            $asunto[] = 'SIN O/C';
        } else {
            $asunto[] = $orden->nro_orden;
            $asunto[] = $orden->entidad->nombre;
        }
        $asunto[] = $this->oportunidad->codigo_oportunidad;
        if ($orden != null) {
            $asunto[] = $orden->empresa->abreviado;
        }
        return $this->view('mgcp.cuadro-costo.email.hoja-transformacion')->subject(implode(' | ', $asunto));
    }
}
