<?php

namespace App\Models\mgcp\Usuario;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use hisorange\BrowserDetect\Parser as Browser;

class LogLogin extends Model
{
    use HasFactory;
    protected $table = 'mgcp_usuarios.logs_login';
    public $timestamps = false;

    public static function registrar(User $usuario, Request $request)
    {
        $log = new LogLogin();
            $log->id_usuario = $usuario->id;
            $log->ip = $request->ip();
            $log->fecha = new Carbon();
            $log->tipo_dispositivo = Browser::deviceType();
            $ubicacion = Location::get($request->ip());
            
            if (is_object($ubicacion)) {
                $log->pais = $ubicacion->countryCode;
                $log->region = $ubicacion->regionName;
                $log->ciudad = $ubicacion->cityName;
            }
        $log->save();
    }

    public function getFechaAttribute() {
        return (new Carbon($this->attributes['fecha']))->format('d-m-Y H:i');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
