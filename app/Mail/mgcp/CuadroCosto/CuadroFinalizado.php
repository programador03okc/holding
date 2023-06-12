<?php

namespace App\Mail\mgcp\CuadroCosto;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CuadroFinalizado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $url;
    public $oportunidad;
    public $autor;

    public function __construct($url, $oportunidad, $autor)
    {
        $this->url = $url;
        $this->oportunidad = $oportunidad;
        $this->autor = $autor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mgcp.cuadro-costo.email.finalizar')
            ->subject('Finalización de cuadro de prespuesto ' . $this->oportunidad->codigo_oportunidad . ' por ' . $this->autor->name);;
    }
}
