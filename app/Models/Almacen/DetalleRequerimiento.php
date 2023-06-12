<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRequerimiento extends Model
{
    use HasFactory;
    protected $table = 'almacen.alm_det_req';
    protected $primaryKey = 'id_detalle_requerimiento';
    public $timestamps = false;
}
