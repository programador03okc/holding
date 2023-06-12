<?php

namespace Database\Factories\mgcp\AcuerdoMarco\Producto;

use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Producto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_categoria' => Categoria::first()->id,
            'descripcion' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'part_no' => $this->faker->word,
            'marca' => $this->faker->word,
            'modelo' => $this->faker->word,
            'moneda'=>'USD'
        ];
    }
}
