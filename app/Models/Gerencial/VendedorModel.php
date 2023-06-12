<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendedorModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.vendedor';
    protected $primaryKey = 'id_vendedor';
    protected $fillable = ['id_vendedor', 'nombre', 'estado'];
}
