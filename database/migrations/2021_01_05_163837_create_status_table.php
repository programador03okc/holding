<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_oportunidades.status', function (Blueprint $table) {
            $table->id();
            $table->text('detalle');
            $table->foreignId('id_oportunidad');
            $table->foreignId('id_usuario');
            $table->timestamps();
            $table->foreignId('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_oportunidades.status');
    }
}
