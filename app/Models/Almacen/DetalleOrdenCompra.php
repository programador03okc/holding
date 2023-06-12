<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenCompra extends Model
{
    use HasFactory;
    protected $table = 'logistica.log_det_ord_compra';
    protected $primaryKey = 'id_detalle_orden';
    public $timestamps = false;
}
