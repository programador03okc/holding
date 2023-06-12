<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcSolicitud extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'mgcp_cuadro_costos.cc_solicitudes';
    protected $appends = ['fecha_solicitud', 'fecha_respuesta', 'aprobacion'];
    // public $timestamps = false;
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /* public function setFechaAttribute() {
      $this->attributes['fecha'] = date('Y-m-d H:i:s');
      } */

    public function enviadaPor()
    {
        return $this->belongsTo(User::class, 'enviada_por')->withTrashed();
    }

    public function enviadaA()
    {
        return $this->belongsTo(User::class, 'enviada_a')->withTrashed();
    }

    public function getFechaSolicitudAttribute()
    {
        return (new Carbon($this->attributes['fecha_solicitud']))->format('d-m-Y');
    }

    public function getFechaRespuestaAttribute()
    {
        return $this->attributes['fecha_respuesta']==null ? '' : (new Carbon($this->attributes['fecha_respuesta']))->format('d-m-Y');
    }

    public function getAprobacionAttribute()
    {
        switch ($this->attributes['aprobada']) {
            case '1':
                return 'Aprobada';
                break;
            case '0':
                return 'No aprobada';
                break;
            default:
                return '';
                break;
        }
    }

    public function getComentarioSolicitanteAttribute()
    {
        return $this->attributes['comentario_solicitante'] ?? "";
    }

    public function getComentarioAprobadorAttribute()
    {
        return $this->attributes['comentario_aprobador'] ?? "";
    }
    /* public function aprobacion() {
      return $this->hasOne('App\Cuadrocosto\Ccrespuestasolicitud', 'id');
      } */

    public function tipoSolicitud()
    {
        return $this->belongsTo(CcTipoSolicitud::class, 'id_tipo');
    }

    public function getMargenCuadroAttribute()
    {
        return $this->attributes['margen_cuadro'] ?? 0;
    }
}
