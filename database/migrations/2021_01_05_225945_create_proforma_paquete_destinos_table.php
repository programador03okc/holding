<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaPaqueteDestinosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proforma_paquete_destinos', function (Blueprint $table) {
            $table->foreignId('nro_requerimiento_entrega');
            $table->text('lugar_entrega');
            $table->date('inicio_entrega');
            $table->date('fin_entrega');
            $table->foreignId('id_departamento');
            $table->integer('nro_requerimiento');
            $table->smallInteger('aplica_igv');
            $table->integer('plazo_publicar');
            $table->boolean('editar_plazo');
            $table->primary('nro_requerimiento_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proforma_paquete_destinos');
    }
}
