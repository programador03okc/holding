<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'almacen.alm_reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;
    protected $appends = ['nombre_estado'];
}
