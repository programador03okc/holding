<?php

namespace Database\Factories\mgcp\Oportunidad;

use App\Models\mgcp\Oportunidad\TipoNegocio;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoNegocioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TipoNegocio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tipo'=>$this->faker->word
        ];
    }
}
