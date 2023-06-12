<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FondoMicrosoft extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_cuadro_costos.fondos_microsoft';
    protected $fillable = ['tipo_bolsa_id', 'part_no', 'descripcion', 'importe', 'tipo', 'estado'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function tipo_bolsa()
    {
        return $this->belongsTo(TipoBolsa::class);
    }
}
