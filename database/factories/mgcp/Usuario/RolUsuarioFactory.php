<?php

namespace Database\Factories\mgcp\Usuario;

use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolUsuarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RolUsuario::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_usuario' => 1,
            'id_rol' => 1
        ];
    }
}
