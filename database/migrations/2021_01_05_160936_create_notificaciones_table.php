<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_usuarios.notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario');
            $table->text('mensaje');
            $table->timestamp('fecha');
            $table->string('url',100);
            $table->boolean('leido');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_usuarios.notificaciones');
    }
}
