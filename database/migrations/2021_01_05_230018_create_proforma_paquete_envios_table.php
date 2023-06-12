<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaPaqueteEnviosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proforma_paquete_envios', function (Blueprint $table) {
            $table->foreignId('nro_item_entrega');
            $table->integer('nro_requerimiento_entrega');
            $table->primary('nro_item_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proforma_paquete_envios');
    }
}
