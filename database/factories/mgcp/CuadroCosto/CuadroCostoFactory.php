<?php

namespace Database\Factories\mgcp\CuadroCosto;

use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CuadroCostoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CuadroCosto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_oportunidad'=>Oportunidad::factory(),
            'tipo_cambio'=>3,
            'igv'=>18,
            'estado_aprobacion'=>1,
            'tipo_cuadro'=>1,
            'porcentaje_responsable'=>100,
            'moneda'=>'s',
            'fecha_creacion'=>Carbon::now()
        ];
    }
}
