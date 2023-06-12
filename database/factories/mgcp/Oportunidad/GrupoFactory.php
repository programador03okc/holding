<?php

namespace Database\Factories\mgcp\Oportunidad;

use App\Models\mgcp\Oportunidad\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Grupo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'grupo'=>$this->faker->word
        ];
    }
}
