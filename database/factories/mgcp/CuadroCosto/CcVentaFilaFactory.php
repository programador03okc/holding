<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\CcVenta;
use App\Models\mgcp\CuadroCosto\CcVentaFila;
use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CcVentaFilaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CcVentaFila::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_cc_venta' => CcVenta::factory(),
            'part_no' => $this->faker->word,
            'descripcion' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'unidad' => 'UND',
            'cantidad' => $this->faker->randomDigitNotNull,
            'flete' => $this->faker->numberBetween(10, 50),
            'margen_ganancia' => $this->faker->numberBetween(10, 15),
            'proveedor_seleccionado' => null,
            'plazo_entrega' => $this->faker->numberBetween(10, 30),
            'garantia' => 36,
            'creado_por' => User::factory(),
            'fecha_creacion' => new Carbon(),
            'comprado' => false,
            'id_origen_costeo' => 1

        ];
    }
}
