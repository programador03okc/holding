<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOportunidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_oportunidades.oportunidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_oportunidad',20);
            $table->foreignId('id_entidad');
            $table->text('oportunidad');
            $table->enum('probabilidad',['alta','media','baja']);
            $table->date('fecha_limite');
            $table->enum('moneda',['s','d']);
            $table->decimal('importe',10,2);
            $table->integer('margen');
            $table->foreignId('id_tipo_negocio');
            $table->foreignId('id_responsable');
            $table->boolean('eliminado');
            $table->timestamps();
            $table->string('nombre_contacto',100)->nullable();
            $table->string('telefono_contacto',50)->nullable();
            $table->string('correo_contacto',50)->nullable();
            $table->foreignId('id_grupo');
            $table->foreignId('id_estado');
            $table->string('cargo_contacto',50)->nullable();
            $table->string('reportado_por',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_oportunidades.oportunidades');
    }
}
