<?php

namespace App\Models\mgcp\OrdenCompra\Publica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompraPublicaProveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.oc_publicas_proveedores';
    protected $fillable = ['id_pc', 'nombre'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
