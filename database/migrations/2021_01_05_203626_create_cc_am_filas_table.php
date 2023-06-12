<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcAmFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_am_filas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc_am');
            $table->string('part_no',50)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('pvu_oc',20,2)->nullable();
            $table->decimal('flete_oc',20,2)->nullable();
            $table->integer('proveedor_seleccionado')->nullable();
            $table->integer('garantia')->nullable();
            $table->foreignId('creado_por');
            $table->timestamp('fecha_creacion');
            $table->boolean('comprado');
            $table->foreignId('id_origen_costeo');
            $table->string('part_no_producto_transformado',50)->nullable();
            $table->text('descripcion_producto_transformado')->nullable();
            $table->text('comentario_producto_transformado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_am_filas');
    }
}
