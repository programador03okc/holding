<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogosAcuerdosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.catalogos_acuerdos', function (Blueprint $table) {
            $table->foreignId('id_acuerdo_marco');
            $table->foreignId('id_catalogo');
            $table->primary(['id_acuerdo_marco', 'id_catalogo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.catalogos_acuerdos');
    }
}
