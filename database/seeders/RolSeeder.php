<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mgcp_usuarios.roles')->insert([
            'id' => 1,
            'descripcion' => Str::random(10),
            'id_tipo'=>1
        ]);
        DB::table('mgcp_usuarios.roles')->insert([
            'id' => 2,
            'descripcion' => Str::random(10),
            'id_tipo'=>1
        ]);
        DB::table('mgcp_usuarios.roles')->insert([
            'id' => 3,
            'descripcion' => Str::random(10),
            'id_tipo'=>1
        ]);
    }
}
