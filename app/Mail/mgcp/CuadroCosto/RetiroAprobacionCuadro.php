<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RetiroAprobacionCuadro extends Mailable
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
    public $requerimiento;
    public $requerimientoFueAnulado;

    public function __construct($solicitud, $url, $oportunidad, $autor, $requerimiento)
    {
        $this->solicitud = $solicitud;
        $this->url = $url;
        $this->oportunidad = $oportunidad;
        $this->autor = $autor;
        $this->requerimiento=$requerimiento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mgcp.cuadro-costo.email.retiro_aprobacion_cuadro')->subject('Retiro de aprobación de cuadro de presupuesto ' . $this->oportunidad->codigo_oportunidad);;
    }
}
