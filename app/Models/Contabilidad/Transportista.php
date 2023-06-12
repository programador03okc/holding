<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    use HasFactory;
    protected $table = 'contabilidad.transportistas';
    protected $primaryKey = 'id_contribuyente';
    public $timestamps = false;
    public $incrementing = false;

    
}
