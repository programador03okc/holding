<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RespuestaSolicitud extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $solicitud;
     public $url;
     public $oportunidad;
     public $autor;
     public $tipoSolicitud;

    public function __construct($solicitud,$url,$oportunidad,$autor,$tipoSolicitud)
    {
        $this->solicitud=$solicitud;
        $this->url=$url;
        $this->oportunidad=$oportunidad;
        $this->autor=$autor;
        $this->tipoSolicitud=$tipoSolicitud;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mgcp.cuadro-costo.email.respuesta_solicitud')->subject('Respuesta de ' . $this->autor->name . ' a solicitud de ' . $this->tipoSolicitud . ' para cuadro de presupuesto ' . $this->oportunidad->codigo_oportunidad);
    }
}
