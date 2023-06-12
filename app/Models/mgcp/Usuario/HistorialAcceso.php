<?php

namespace App\Models\mgcp\Usuario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialAcceso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_usuarios.historial_acceso';

    protected $fillable = ['fecha', 'id_usuario', 'ip', 'mac'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
