<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformasPaqueteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proformas_paquete', function (Blueprint $table) {
            $table->foreignId('nro_requerimiento');
            $table->foreignId('id_empresa');
            $table->string('requerimiento',200);
            $table->foreignId('id_entidad');
            $table->date('fecha_emision');
            $table->date('fecha_limite');
            $table->string('estado',45);
            $table->foreignId('tipo');
            $table->foreignId('id_ultimo_usuario')->nullable();
            $table->timestamp('fecha_cotizacion')->nullable();
            $table->primary('nro_requerimiento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proformas_paquete');
    }
}
