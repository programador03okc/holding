<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalidad extends Model
{
    use HasFactory;
    protected $table = 'gerencial.penalidad';
    protected $primaryKey = 'id_penalidad';
    public $timestamps = false;
}
