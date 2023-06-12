<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_venta', function (Blueprint $table) {
            $table->foreignId('id_cc');
            $table->decimal('margen_preferencial',20,2);
            $table->date('fecha_entrega')->nullable();
            $table->primary('id_cc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_venta');
    }
}
