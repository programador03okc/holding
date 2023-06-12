<?php

namespace App\Models\mgcp\OrdenCompra\Publica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompraPublicaAnalisis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.oc_publicas_analisis';

    protected $fillable = [
        'fecha', 'id_entidad', 'id_proveedor', 'id_producto', 'marca', 'modelo', 'part_number', 'procesador', 'precio_costo', 'cantidad', 'id_empresa',
        'precio_dolares', 'precio_soles', 'fecha_convocatoria', 'total', 'margen', 'marca_ext', 'modelo_ext', 'part_number_ext', 'procesador_ext',
        'precio_costo_ext', 'precio_dolares_ext', 'total_ext', 'margen_ext'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
