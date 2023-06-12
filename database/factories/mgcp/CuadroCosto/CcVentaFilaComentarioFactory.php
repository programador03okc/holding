<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\CcVentaFila;
use App\Models\mgcp\CuadroCosto\CcVentaFilaComentario;
use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CcVentaFilaComentarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CcVentaFilaComentario::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_usuario' => User::factory(),
            'fecha' => Carbon::now(),
            'comentario' => $this->faker->text(200),
            'id_fila' => CcVentaFila::factory()
        ];
    }
}
