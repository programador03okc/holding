<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcuerdoMarcoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.acuerdo_marco', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',20);
            $table->integer('id_pc');
            $table->boolean('activo');
            $table->string('descripcion_corta',20);
            $table->string('descripcion_larga',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.acuerdo_marco');
    }
}
