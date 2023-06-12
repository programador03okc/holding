<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaPaqueteProductoDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proforma_paquete_producto_detalles', function (Blueprint $table) {
            $table->foreignId('nro_proforma');
            $table->foreignId('id_producto');
            $table->string('moneda_ofertada',10);
            $table->string('proforma',200);
            $table->boolean('software_educativo');
            $table->integer('cantidad');
            $table->decimal('precio_unitario_base',20,2);
            $table->decimal('precio_publicar',20,2)->nullable();
            $table->boolean('seleccionado');
            $table->integer('nro_requerimiento_item');
            $table->primary('nro_proforma');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proforma_paquete_producto_detalles');
    }
}
