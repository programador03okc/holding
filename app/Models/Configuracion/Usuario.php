<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'configuracion.sis_usua';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;
}
