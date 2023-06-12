<?php

namespace Database\Factories\mgcp\CuadroCosto\Ajuste;

use App\Models\mgcp\CuadroCosto\Ajuste\FondoIngreso;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedor;
use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FondoIngresoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FondoIngreso::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_fondo_proveedor' => FondoProveedor::factory(),
            'cantidad' => $this->faker->numberBetween(10,100),
            'fecha' => new Carbon(),
            'id_usuario' => User::factory()
        ];
    }
}
