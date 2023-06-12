<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.empresa';
    protected $primaryKey = 'id_empresa';
    protected $fillable = ['id_empresa', 'ruc', 'nombre', 'codigo', 'estado'];
}
