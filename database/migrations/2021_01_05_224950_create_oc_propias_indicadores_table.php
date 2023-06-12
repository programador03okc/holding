<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcPropiasIndicadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.oc_propias_indicadores', function (Blueprint $table) {
            $table->foreignId('tipo');
            $table->decimal('amarillo',20,2);
            $table->decimal('rojo',20,2);
            $table->primary('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.oc_propias_indicadores');
    }
}
