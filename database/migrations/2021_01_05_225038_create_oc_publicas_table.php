<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcPublicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.oc_publicas', function (Blueprint $table) {
            $table->foreignId('id');
            $table->string('orden_compra');
            $table->foreignId('id_entidad');
            $table->string('ruc_proveedor',20);
            $table->string('razon_social');
            $table->integer('plazo_entrega')->nullable();
            $table->date('fecha_formalizacion')->nullable();
            $table->foreignId('id_provincia');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.oc_publicas');
    }
}
