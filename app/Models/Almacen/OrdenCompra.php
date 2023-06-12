<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;
    protected $table = 'logistica.log_ord_compra';
    protected $primaryKey = 'id_orden_compra';
    public $timestamps = false;
}
