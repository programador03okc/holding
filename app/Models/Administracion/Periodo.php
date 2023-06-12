<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;
    protected $table = 'administracion.adm_periodo';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;
}
