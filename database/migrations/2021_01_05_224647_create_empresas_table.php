<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.empresas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa',45);
            $table->string('ruc',12);
            $table->string('password',45);
            $table->integer('id_pc');
            $table->string('usuario2',45);
            $table->string('password2',45);
            $table->string('nombre_corto',10);
            $table->smallInteger('indicador_semaforo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.empresas');
    }
}
