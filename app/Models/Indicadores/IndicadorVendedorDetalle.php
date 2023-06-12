<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorVendedorDetalle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'indicadores.kpi_gad_vendedor_detalle';
    protected $primaryKey = 'id_kpi_detalle';
    protected $fillable = ['id_kpi_vendedor', 'id_vendedor', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dic'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
