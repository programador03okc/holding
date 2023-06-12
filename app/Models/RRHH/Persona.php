<?php

namespace App\Models\RRHH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $table = 'rrhh.rrhh_perso';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;
}
