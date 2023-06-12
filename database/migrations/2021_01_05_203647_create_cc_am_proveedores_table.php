<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcAmProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_am_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fila');
            $table->foreignId('id_proveedor');
            $table->decimal('precio',20,2);
            $table->string('moneda',1);
            $table->integer('plazo');
            $table->decimal('flete',20,2);
            $table->text('comentario')->nullable();
            $table->foreignId('id_fondo_proveedor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_am_proveedores');
    }
}
