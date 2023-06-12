<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcVentaFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_venta_filas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc_venta');
            $table->string('part_no',50)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('unidad',25)->nullable();
            $table->decimal('cantidad',20,2)->nullable();
            $table->decimal('flete',20,2)->nullable();
            $table->decimal('margen_ganancia',20,2)->nullable();
            $table->foreignId('proveedor_seleccionado')->nullable();
            $table->integer('plazo_entrega')->nullable();
            $table->integer('garantia')->nullable();
            $table->foreignId('creado_por');
            $table->timestamp('fecha_creacion');
            $table->boolean('comprado');
            $table->foreignId('id_origen_costeo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_venta_filas');
    }
}
