<?php

namespace App\Models;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\Usuario\RolUsuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table = 'mgcp_usuarios.users';
    protected $fillable = ['name', 'email', 'password', 'fecha_renovacion', 'id_empresa'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
        
    }

    public function tieneRol($rol)
    {
        if (RolUsuario::where('id_usuario', $this->id)->where('id_rol', $rol)->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function obtenerPorRol($rol)
    {
        return User::withTrashed()->whereRaw('id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE id_rol=?)', [$rol])->orderBy('name', 'asc')->get();
    }
}
