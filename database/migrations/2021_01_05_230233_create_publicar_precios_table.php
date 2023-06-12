<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicarPreciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.publicar_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa');
            $table->foreignId('id_producto');
            $table->decimal('precio',20,2);
            $table->boolean('publicado');
            $table->smallInteger('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.publicar_precios');
    }
}
