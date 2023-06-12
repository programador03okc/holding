<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcBsFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_bs_filas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc_bs');
            $table->string('part_no',50)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('unidad',25)->nullable();
            $table->decimal('cantidad',20,2)->nullable();
            $table->integer('proveedor_seleccionado')->nullable();
            $table->foreignId('creado_por');
            $table->timestamp('fecha_creacion');
            $table->boolean('comprado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_bs_filas');
    }
}
