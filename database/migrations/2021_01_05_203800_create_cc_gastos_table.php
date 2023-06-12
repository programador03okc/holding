<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.cc_gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cc');
            $table->foreignId('id_gasto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.cc_gastos');
    }
}
