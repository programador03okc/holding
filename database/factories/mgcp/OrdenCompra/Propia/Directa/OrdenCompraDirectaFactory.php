<?php

namespace Database\Factories\mgcp\OrdenCompra\Propia\Directa;

use App\Helpers\mgcp\OrdenCompraDirectaHelper;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Etapa;
use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\mgcp\AcuerdoMarco\Entidad\EntidadFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenCompraDirectaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrdenCompraDirecta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nro_orden'=>OrdenCompraDirectaHelper::generarCodigo(),
            'id_empresa'=>Empresa::first()->id,
            'id_entidad'=>Entidad::factory(),
            'lugar_entrega'=>$this->faker->address,
            'monto_total'=>$this->faker->numberBetween(1000, 10000),
            'id_etapa'=>Etapa::first()->id,
            'id_corporativo'=>User::factory(),
            'cobrado'=>false,
            'conformidad'=>false,
            'siaf'=>null,
            'codigo_gasto'=>null,
            'eliminado'=>false,
            'fecha_entrega'=>Carbon::now()->format('d-m-Y'),
            'id_oportunidad'=>null,
            'id_contacto'=>null,
            'fecha_publicacion'=>Carbon::now()->format('d-m-Y'),
            'occ'=>null,
            'despachada'=>false,
        ];
    }
}
