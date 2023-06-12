<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCostoModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.centro_costo';
    protected $primaryKey = 'id_centro_costo';
    protected $fillable = ['id_centro_costo', 'codigo', 'id_padre', 'descripcion', 'nivel', 'estado', 'version'];
}
