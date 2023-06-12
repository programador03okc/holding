<?php

namespace Database\Factories\mgcp\OrdenCompra\Propia\Directa;

use App\Models\mgcp\OrdenCompra\Propia\Directa\ComentarioOcDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComentarioOcDirectaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ComentarioOcDirecta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_oc' => OrdenCompraDirecta::factory(),
            'id_usuario' => User::factory(),
            'fecha' => new Carbon(),
            'comentario' => $this->faker->text(200)
        ];
    }
}
