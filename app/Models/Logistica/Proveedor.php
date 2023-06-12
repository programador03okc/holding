<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'logistica.log_prove';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;
}
