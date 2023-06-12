<?php

namespace App\Models\RRHH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;
    protected $table = 'rrhh.rrhh_trab';
    protected $primaryKey = 'id_trabajador';
    public $timestamps = false;
}
