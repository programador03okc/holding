<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosAmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mgcp_acuerdo_marco.productos_am', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_categoria');
            $table->text('descripcion');
            $table->text('part_no');
            $table->text('marca');
            $table->text('modelo');
            $table->string('imagen',200)->nullable();
            $table->string('ficha_tecnica',200)->nullable();
            $table->string('moneda',5)->nullable();
            $table->integer('id_okc')->nullable();
            $table->decimal('precio_okc',10,2)->nullable();
            $table->decimal('puntaje_okc',10,2)->nullable();

            $table->integer('id_proy')->nullable();
            $table->decimal('precio_proy',10,2)->nullable();
            $table->decimal('puntaje_proy',10,2)->nullable();
            
            $table->integer('id_smart')->nullable();
            $table->decimal('precio_smart',10,2)->nullable();
            $table->decimal('puntaje_smart',10,2)->nullable();
            
            $table->integer('id_deza')->nullable();
            $table->decimal('precio_deza',10,2)->nullable();
            $table->decimal('puntaje_deza',10,2)->nullable();
            
            $table->integer('id_dorado')->nullable();
            $table->decimal('precio_dorado',10,2)->nullable();
            $table->decimal('puntaje_dorado',10,2)->nullable();

            $table->integer('id_pc')->nullable();
            $table->boolean('nuevo')->nullable();

            $table->integer('id_protec')->nullable();
            $table->decimal('precio_protec',10,2)->nullable();
            $table->decimal('puntaje_protec',10,2)->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mgcp_acuerdo_marco.productos_am');
    }
}
