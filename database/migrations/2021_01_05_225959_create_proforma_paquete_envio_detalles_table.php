<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaPaqueteEnvioDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proforma_paquete_envio_detalles', function (Blueprint $table) {
            $table->foreignId('nro_detalle_entrega');
            $table->integer('nro_proforma');
            $table->decimal('costo_envio_publicar',20,2)->nullable();
            $table->boolean('requiere_flete');
            $table->integer('nro_item_entrega');
            $table->primary('nro_detalle_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proforma_paquete_envio_detalles');
    }
}
