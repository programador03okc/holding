<?php

namespace App\Models\RRHH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    use HasFactory;
    protected $table = 'rrhh.rrhh_postu';
    protected $primaryKey = 'id_postulante';
    public $timestamps = false;
}
