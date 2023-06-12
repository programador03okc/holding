<?php

namespace App\Models\mgcp\Usuario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialRenovacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_usuarios.historial_renovacion_claves';

    protected $fillable = ['fecha', 'id_usuario'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
