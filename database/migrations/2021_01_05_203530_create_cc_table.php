<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_oportunidad');
            $table->decimal('tipo_cambio',10,3);
            $table->integer('igv');
            $table->integer('estado_aprobacion');
            $table->integer('tipo_cuadro');
            $table->integer('porcentaje_responsable');
            $table->string('moneda',1);
            $table->date('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc');
    }
}
