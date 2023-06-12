<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\Proveedor;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proveedor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ruc' => $this->faker->numberBetween(20000000, 20999999),
            'razon_social' => $this->faker->company,
        ];
    }
}
