<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioSede extends Model
{
    use HasFactory;
    protected $table = 'configuracion.sis_usua_sede';
    protected $primaryKey = 'id_usua_sede';
    public $timestamps = false;
}
