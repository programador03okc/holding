<?php

namespace Database\Factories\mgcp\Oportunidad;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\Oportunidad\Estado;
use App\Models\mgcp\Oportunidad\Grupo;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\Oportunidad\TipoNegocio;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OportunidadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Oportunidad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'codigo_oportunidad' => 'OKC' . $this->faker->numberBetween(1000000, 9999999),
            'oportunidad' => $this->faker->sentence(6),
            'probabilidad' => 'alta',
            'fecha_limite' => date('d-m-Y'),
            'id_entidad' => Entidad::factory(), 
            'id_responsable' => User::factory(),
            'moneda' => 's',
            'importe' => $this->faker->numberBetween(1000, 10000),
            'margen' => 10,
            'id_tipo_negocio' => TipoNegocio::factory(), 
            'eliminado' => false,
            'id_grupo' => Grupo::factory(),
            'id_estado' => Estado::factory(), 
        ];
    }
}
