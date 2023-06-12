<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = 'almacen.alm_prod';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;
}
