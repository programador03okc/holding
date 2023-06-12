<?php

namespace App\Models\Indicadores;

use App\Models\Administracion\Periodo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorAnual extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'indicadores.kpi_gad_anual';
    protected $primaryKey = 'id_kpi_anual';
    protected $fillable = ['fecha', 'id_periodo', 'mes', 'tipo', 'monto_anual', 'monto_q1', 'monto_q2', 'monto_q3', 'monto_q4'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
