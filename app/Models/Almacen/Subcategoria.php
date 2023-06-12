<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;
    protected $table = 'almacen.alm_subcat';
    protected $primaryKey = 'id_subcategoria';
    public $timestamps = false;
}
