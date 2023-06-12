<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mgcp_usuarios.users', function (Blueprint $table) {
            $table->boolean('activo');
            $table->string('nombre_corto',15);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mgcp_usuarios.users', function (Blueprint $table) {
            $table->dropColumn(['activo', 'nombre_corto']);
        });
    }
}
