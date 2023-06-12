<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc');
            $table->timestamp('fecha_solicitud');
            $table->foreignId('enviada_por');
            $table->foreignId('id_tipo');
            $table->foreignId('enviada_a');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->boolean('aprobada')->nullable();
            $table->integer('estado_cuadro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_solicitudes');
    }
}
