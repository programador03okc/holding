<?php

namespace Database\Factories\mgcp\CuadroCosto\Ajuste;

use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedor;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class FondoProveedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FondoProveedor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'descripcion' => $this->faker->sentence(3),
            'moneda' => 'd',
            'valor_unitario' => $this->faker->numberBetween(1, 1000),
            'activo' => true

        ];
    }
}
