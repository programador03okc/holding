<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_cuadro_costos.gastos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto',150);
            $table->foreignId('id_operacion');
            $table->foreignId('id_afectacion');
            $table->decimal('porcentaje',5,2);
            $table->decimal('desde',20,2);
            $table->decimal('hasta',20,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_cuadro_costos.gastos');
    }
}
