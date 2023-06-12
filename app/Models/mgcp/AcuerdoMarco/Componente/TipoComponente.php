<?php

namespace App\Models\mgcp\AcuerdoMarco\Componente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoComponente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.cp_tipo_componente';
    protected $fillable = ['descripcion'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
