<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcDirectasComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_ordenes_compra.oc_directas_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_oc');
            $table->foreignId('id_usuario');
            $table->timestamp('fecha');
            $table->text('comentario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_ordenes_compra.oc_directas_comentarios');
    }
}
