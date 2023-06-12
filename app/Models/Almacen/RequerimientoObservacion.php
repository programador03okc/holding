<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoObservacion extends Model
{
    use HasFactory;
    protected $table = 'almacen.alm_req_obs';
    protected $primaryKey = 'id_observacion';
    public $timestamps = false;
}
