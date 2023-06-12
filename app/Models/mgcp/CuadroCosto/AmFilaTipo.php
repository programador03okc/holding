<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmFilaTipo extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.am_fila_tipos';
    public $timestamps = false;
}
