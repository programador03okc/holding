<?php

namespace Database\Factories\mgcp\Oportunidad;

use App\Models\mgcp\Oportunidad\Estado;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estado::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'estado'=>$this->faker->word
        ];
    }
}
