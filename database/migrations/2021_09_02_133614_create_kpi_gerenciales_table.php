<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiGerencialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadores.kpi_gerenciales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('id_periodo');
            $table->integer('mes')->nullable();
            $table->string('tipo', 30);
            $table->decimal('monto_anual', 9, 2);
            $table->decimal('monto_mensual', 9, 2);
            $table->decimal('monto_q1', 9, 2);
            $table->decimal('monto_q2', 9, 2);
            $table->decimal('monto_q3', 9, 2);
            $table->decimal('monto_q4', 9, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indicadores.kpi_gerenciales');
    }
}
