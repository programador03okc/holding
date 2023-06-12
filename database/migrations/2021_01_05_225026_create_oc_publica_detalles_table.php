<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcPublicaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.oc_publica_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_orden_compra');
            $table->foreignId('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario',20,2);
            $table->decimal('costo_envio',20,2);
            $table->decimal('igv',20,2);
            $table->decimal('importe',20,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.oc_publica_detalles');
    }
}
