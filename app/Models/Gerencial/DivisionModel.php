<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.division';
    protected $primaryKey = 'id_division';
    protected $fillable = ['id_division', 'descripcion', 'estado'];
}
