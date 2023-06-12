<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcPropiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.oc_propias', function (Blueprint $table) {
            $table->foreignId('id');
            $table->string('orden_am',50);
            $table->foreignId('id_empresa');
            $table->foreignId('id_entidad');
            $table->string('estado_oc',50);
            $table->timestamp('fecha_estado');
            $table->integer('plazo_dias')->nullable();
            $table->string('lugar_entrega');
            $table->decimal('monto_total',20,2);
            $table->string('factura',50)->nullable();
            $table->string('guia',50)->nullable();
            $table->date('fecha_guia')->nullable();
            $table->string('orden_compra',50)->nullable();
            $table->foreignId('id_etapa');
            $table->foreignId('id_corporativo')->nullable();
            $table->boolean('cobrado');
            $table->boolean('conformidad');
            $table->string('siaf',50)->nullable();;
            $table->string('codigo_gasto',50)->nullable();
            $table->boolean('eliminado');
            $table->foreignId('id_tipo');
            $table->string('url_oc_fisica',200);
            $table->date('fecha_entrega')->nullable();
            $table->foreignId('id_oportunidad')->nullable();
            $table->boolean('paquete');
            $table->integer('id_alternativo');
            $table->string('estado_entrega')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->foreignId('id_catalogo')->nullable();
            $table->string('occ',40)->nullable();
            $table->boolean('despachada');
            $table->foreignId('id_contacto')->nullable();
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
        Schema::dropIfExists('mgcp_acuerdo_marco.oc_propias');
    }
}
