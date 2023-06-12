<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudAprobacion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $tipoSolicitud; 
    public $cuadro; 
    public $comentario;
    public $url;
    public $oportunidad; 
    public $autor;
    public $asunto;

    public function __construct($tipoSolicitud,$cuadro,$comentario,$url,$oportunidad,$autor,$asunto)
    {
        $this->tipoSolicitud=$tipoSolicitud;
        $this->cuadro=$cuadro;
        $this->comentario=$comentario;
        $this->url=$url;
        $this->oportunidad=$oportunidad;
        $this->autor=$autor;
        $this->asunto=$asunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mgcp.cuadro-costo.email.nueva_solicitud')
        ->subject('Nueva solicitud de ' . $this->asunto . ' para cuadro de presupuesto ' . $this->oportunidad->codigo_oportunidad . ' por ' . $this->autor->name);
    }
}
