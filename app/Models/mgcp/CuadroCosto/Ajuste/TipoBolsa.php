<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoBolsa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_cuadro_costos.tipo_bolsa';
    protected $fillable = ['descripcion', 'importe'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
