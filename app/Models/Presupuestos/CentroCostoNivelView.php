<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class CentroCostoNivelView extends Model
{
    protected $table = 'finanzas.cc_niveles_view';
    public $incrementing = false;
    protected $primaryKey = 'id_centro_costo';
    public $timestamps = false;
}
