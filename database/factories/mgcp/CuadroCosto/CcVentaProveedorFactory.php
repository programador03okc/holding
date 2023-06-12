<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\CcVentaFila;
use App\Models\mgcp\CuadroCosto\CcVentaProveedor;
use App\Models\mgcp\CuadroCosto\Proveedor;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class CcVentaProveedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CcVentaProveedor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_fila' => CcVentaFila::factory(),
            'id_proveedor' => Proveedor::factory(),
            'precio' => $this->faker->randomFloat(2, 1, 1000),
            'moneda' => 'd',
            'plazo' => $this->faker->numberBetween(1, 36),
            'flete' => $this->faker->numberBetween(10, 45),
            'comentario' => $this->faker->sentence(6),
            'id_fondo_proveedor' => null
        ];
    }
}
