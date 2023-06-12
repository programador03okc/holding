<?php

namespace Database\Factories\mgcp\AcuerdoMarco\Entidad;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntidadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entidad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ruc' => $this->faker->numerify('21#########'), //21 para evitar que cree un RUC que ya exista en la BD
            'nombre' => $this->faker->company,
            'direccion' => $this->faker->address,
            'ubigeo' => 'LIMA / LIMA / LIMA',
            'responsable' => $this->faker->name,
            'telefono' => $this->faker->phoneNumber,
            'cargo' => $this->faker->jobTitle,
            'correo' => $this->faker->email,
            'indicador_semaforo' => $this->faker->numberBetween(0, 3)
        ];
    }
}
