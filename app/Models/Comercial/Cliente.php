<?php

namespace App\Models\Comercial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'comercial.com_cliente';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
}
