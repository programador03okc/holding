<?php

namespace App\Models\mgcp\AcuerdoMarco\Componente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Componente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.cp_componente';
    protected $fillable = ['tipo_componente_id', 'descripcion', 'valor_salida', 'valor_entrada'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
