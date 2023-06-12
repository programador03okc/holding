<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFondosIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.fondos_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fondo_proveedor');
            $table->integer('cantidad');
            $table->timestamp('fecha');
            $table->foreignId('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.fondos_ingresos');
    }
}
