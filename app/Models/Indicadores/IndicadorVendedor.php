<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorVendedor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'indicadores.kpi_gad_vendedor';
    protected $primaryKey = 'id_kpi_vendedor';
    protected $fillable = ['fecha', 'id_periodo'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
