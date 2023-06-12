<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaProveedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProformaAnalisis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.proformas_analisis';

    protected $fillable = [
        'id_proforma', 'tipo_proforma', 'id_proveedor', 'id_producto', 'cantidad', 'tipo_cambio', 'precio_costo', 'precio_soles', 'precio_dolares', 'total', 'margen'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function proveedor()
    {
        return $this->belongsTo(OrdenCompraPublicaProveedor::class, 'id_proveedor', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
}
