<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorMensual extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'indicadores.kpi_gad_mensual';
    protected $primaryKey = 'id_kpi_mensual';
    protected $fillable = ['fecha', 'id_periodo', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dic'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
