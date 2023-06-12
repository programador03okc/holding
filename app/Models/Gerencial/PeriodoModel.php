<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.periodo';
    protected $primaryKey = 'id_periodo';
    protected $fillable = ['id_periodo', 'descripcion', 'estado'];
}
