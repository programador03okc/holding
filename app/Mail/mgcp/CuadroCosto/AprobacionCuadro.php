<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AprobacionCuadro extends Mailable
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
    public $replicacionRequerimiento;

    public function __construct($solicitud, $url, $oportunidad, $autor, $replicacionRequerimiento)
    {
        $this->solicitud = $solicitud;
        $this->url = $url;
        $this->oportunidad = $oportunidad;
        $this->autor = $autor;
        $this->replicacionRequerimiento=$replicacionRequerimiento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mgcp.cuadro-costo.email.cuadro_aprobado')->subject('Aprobación de cuadro de presupuesto ' . $this->oportunidad->codigo_oportunidad);;
    }
}
