<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    protected $table = 'administracion.adm_aprobacion';
    protected $primaryKey = 'id_aprobacion';
    public $timestamps = false;

}
