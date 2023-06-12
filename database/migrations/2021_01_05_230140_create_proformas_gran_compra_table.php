<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformasGranCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.proformas_gran_compra', function (Blueprint $table) {
            $table->foreignId('id_empresa');
            $table->foreignId('id_producto');
            $table->foreignId('nro_proforma');
            $table->foreignId('id_entidad');
            $table->date('fecha_emision');
            $table->string('moneda_ofertada',10);
            $table->integer('cantidad');
            $table->text('lugar_entrega');
            $table->string('estado',45);
            $table->integer('plazo_publicar')->nullable();
            $table->decimal('precio_publicar',20,2)->nullable();
            $table->decimal('costo_envio_publicar',20,2)->nullable();
            $table->foreignId('id_ultimo_usuario')->nullable();
            $table->timestamp('fecha_cotizacion')->nullable();
            $table->date('fecha_limite');
            $table->boolean('software_educativo');
            $table->integer('nro_requerimiento');
            $table->string('proforma',200);
            $table->string('requerimiento',200);
            $table->decimal('precio_unitario_base',20,2);
            $table->boolean('requiere_flete');
            $table->boolean('aplica_igv');
            $table->integer('pcompra_detalle_entrega');
            $table->date('inicio_entrega');
            $table->date('fin_entrega');
            $table->integer('id_departamento');
            $table->primary('nro_proforma');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.proformas_gran_compra');
    }
}
