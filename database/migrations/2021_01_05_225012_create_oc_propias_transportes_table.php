<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcPropiasTransportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.oc_propias_transportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_oc');
            $table->foreignId('id_transportista');
            $table->date('fecha');
            $table->string('nro_guia',50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.oc_propias_transportes');
    }
}
