<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcGgFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_gg_filas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc_gg');
            $table->text('descripcion')->nullable();
            $table->foreignId('id_categoria_gasto');
            $table->string('unidad',25)->nullable();
            $table->integer('personas')->nullable();
            $table->integer('porcentaje_participacion')->nullable();
            $table->decimal('tiempo',20,2)->nullable();
            $table->decimal('costo',20,2)->nullable();
            $table->foreignId('creado_por');
            $table->timestamp('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_gg_filas');
    }
}
