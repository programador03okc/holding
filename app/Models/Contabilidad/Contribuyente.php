<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    use HasFactory;
    protected $table = 'contabilidad.adm_contri';
    protected $primaryKey = 'id_contribuyente';
    public $timestamps = false;
}
