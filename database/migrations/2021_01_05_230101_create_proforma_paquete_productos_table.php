<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaPaqueteProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proforma_paquete_productos', function (Blueprint $table) {
            $table->foreignId('nro_requerimiento_item');
            $table->integer('nro_requerimiento');
            $table->text('comentario');
            $table->primary('nro_requerimiento_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proforma_paquete_productos');
    }
}
