<?php

namespace App\Models\mgcp\CuadroCosto\Ajuste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoFondoMicrosoft extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_cuadro_costos.movimientos_fondo_microsoft';
    protected $fillable = ['tipo_movimiento', 'tipo_bolsa_id', 'fondo_microsoft_origen_id', 'fondo_microsoft_destino_id', 'motivo', 'fecha', 'aprobacion', 'importe'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function tipo_bolsa()
    {
        return $this->belongsTo(TipoBolsa::class);
    }
    
    public function fondo_microsoft_origen()
    {
        return $this->belongsTo(FondoMicrosoft::class);
    }

    public function fondo_microsoft_destino()
    {
        return $this->belongsTo(FondoMicrosoft::class);
    }
}
