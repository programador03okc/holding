<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\CcVenta;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class CcVentaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CcVenta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_cc'=>CuadroCosto::factory(),
            'margen_preferencial'=>15,
            'fecha_entrega'=>'10-10-2020'
        ];
    }
}
