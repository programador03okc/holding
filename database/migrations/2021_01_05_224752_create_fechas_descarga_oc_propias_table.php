<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFechasDescargaOcPropiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.fechas_descarga_oc_propias', function (Blueprint $table) {
            $table->foreignId('id_empresa');
            $table->timestamp('fecha_descarga');
            $table->primary('id_empresa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.fechas_descarga_oc_propias');
    }
}
