<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFletesProformasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.fletes_proformas', function (Blueprint $table) {
            $table->id();
            $table->integer('monto_hasta');
            $table->integer('flete_minimo');
            $table->integer('flete_maximo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.fletes_proformas');
    }
}
