<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.entidades', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20)->nullable();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('ubigeo')->nullable();
            $table->string('responsable')->nullable();
            $table->string('telefono')->nullable();
            $table->string('cargo')->nullable();
            $table->string('correo')->nullable();
            $table->smallInteger('indicador_semaforo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.entidades');
    }
}
