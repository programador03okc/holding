<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAprobadoresTipoDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.aprobadores_tipo_dos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario1');
            $table->foreignId('id_usuario2');
            $table->decimal('costo_maximo',20,2);
            $table->integer('margen_minimo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.aprobadores_tipo_dos');
    }
}
