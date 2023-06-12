<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcVentaFilaComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_venta_fila_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario');
            $table->date('fecha');
            $table->string('comentario');
            $table->foreignId('id_fila');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_venta_fila_comentarios');
    }
}
