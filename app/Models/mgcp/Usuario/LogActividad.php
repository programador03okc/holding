<?php

namespace App\Models\mgcp\Usuario;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActividad extends Model
{
    use HasFactory;
    protected $table = 'mgcp_usuarios.logs_actividades';
    public $timestamps = false;

    public static function registrar(User $usuario, $formulario, $idAccion, $tabla = null, $valorAnterior = null, $nuevoValor = null, $comentarios = null)
    {
        $log = new LogActividad();
            $log->id_usuario = $usuario->id;
            $log->fecha = new Carbon();
            $log->formulario = $formulario;
            $log->id_accion = $idAccion;
            $log->tabla = $tabla;
            if ($valorAnterior != null) { $log->valor_anterior = json_encode($valorAnterior, JSON_PRETTY_PRINT); }
            if ($nuevoValor != null) { $log->nuevo_valor = json_encode($nuevoValor, JSON_PRETTY_PRINT); }
            $log->comentarios = $comentarios;
        $log->save();
    }

    public function getFechaAttribute()
    {
        return (new Carbon($this->attributes['fecha']))->format('d-m-Y H:i:s');
    }
}
