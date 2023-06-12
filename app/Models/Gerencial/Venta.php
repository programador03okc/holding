<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $table = 'gerencial.venta';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;
}
