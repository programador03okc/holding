<?php

namespace App\Mail\mgcp\CuadroCosto;

use App\Helpers\mgcp\OrdenCompraAmHelper;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdenDespacho extends Mailable
{
    use Queueable, SerializesModels;

    public $oportunidad;
    public $mensaje;
    public $archivos;

    public function __construct($oportunidad, $mensaje, $archivos)
    {
        $this->oportunidad = $oportunidad;
        $this->mensaje = $mensaje;
        $this->archivos = $archivos;
    }

    public function build()
    {
        //Creación de asunto de correo
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
        //Vista Email
        $vista = $this->view('mgcp.cuadro-costo.email.orden-despacho')->subject(implode(' | ', $asunto));
        foreach ($this->archivos as $archivo) {
            $vista->attach($archivo);
        }
        //Descarga de archivos de O/C para adjuntarlos
        /*$ordenView = $this->oportunidad->ordenCompraPropia;
        if ($ordenView != null) {
            
            if ($ordenView->tipo == 'am') {
                $archivos=OrdenCompraAmHelper::descargarArchivos($ordenView->id);
                ->attach($archivos[1]);
            }
        }*/
        return $vista;
    }
}
