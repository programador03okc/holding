<?php

namespace App\Models\mgcp\Integracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoCeam extends Model {

    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.productos_ceam';
    protected $fillable = ['acuerdo_marco', 'catalogo', 'categoria', 'producto', 'part_no', 'marca', 'imagen', 'ficha_tecnica', 'estado', 'activo', 'tipo'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}