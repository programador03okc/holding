<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcDirectasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_ordenes_compra.oc_directas', function (Blueprint $table) {
            $table->id();
            $table->string('nro_orden',50);
            $table->foreignId('id_empresa');
            $table->foreignId('id_entidad');
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
            $table->string('siaf',50)->nullable();
            $table->string('codigo_gasto',50)->nullable();
            $table->boolean('eliminado');
            $table->date('fecha_entrega')->nullable();
            $table->foreignId('id_oportunidad')->nullable();
            $table->foreignId('id_contacto')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->string('occ',40)->nullable();
            $table->boolean('despachada');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_ordenes_compra.oc_directas');
    }
}
