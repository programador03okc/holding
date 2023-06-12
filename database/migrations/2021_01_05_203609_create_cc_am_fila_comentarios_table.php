<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcAmFilaComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_am_fila_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario');
            $table->date('fecha');
            $table->text('comentario');
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
        Schema::dropIfExists('mgcp_cuadro_costos.cc_am_fila_comentarios');
    }
}
