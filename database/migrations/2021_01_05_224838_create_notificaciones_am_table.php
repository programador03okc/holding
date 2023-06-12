<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesAmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa');
            $table->foreignId('emitido_por');
            $table->foreignId('destinatario');
            $table->foreignId('id_acuerdo_marco');
            $table->string('orden_compra')->nullable();
            $table->text('asunto');
            $table->string('estado');
            $table->timestamp('fecha');
            $table->integer('plazo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.notificaciones_am');
    }
}
