<?php

namespace App\Models\mgcp\CuadroCosto;

use Carbon\Carbon;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\CuadroCosto\Responsable;
use App\Models\mgcp\CuadroCosto\EstadoAprobacion;
use App\Helpers\mgcp\CuadroCosto\CuadroCostoHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CuadroCostoView extends Model
{
    protected $table = 'mgcp_cuadro_costos.cc_view';
    protected $appends = ['margen_ganancia', 'monto_ganancia', 'tiene_transformacion'];
    public $timestamps = false;

    public static function listar($request)
    {
        /*$data = CuadroCostoView::with('oportunidad')->join('mgcp_oportunidades.oportunidades', 'id_oportunidad', '=', 'oportunidades.id')
            ->join('mgcp_usuarios.users', 'oportunidades.id_responsable', '=', 'users.id')
            ->join('mgcp_acuerdo_marco.entidades', 'oportunidades.id_entidad', '=', 'entidades.id')
            ->join('mgcp_cuadro_costos.estados_aprobacion', 'estado_aprobacion', '=', 'estados_aprobacion.id');*/

        $filtros['eliminado'] = false;
        if ($request->session()->has('ccFiltroEstado')) {
            $filtros['id_estado_aprobacion'] = session('ccFiltroEstado');
        }
        if ($request->session()->has('ccFiltroTipo')) {
            $filtros['tipo_cuadro'] = session('ccFiltroTipo');
        }
        if ($request->session()->has('ccFiltroResponsableOportunidad')) {
            $filtros['id_responsable_oportunidad'] = session('ccFiltroResponsableOportunidad');
        }
        if ($request->session()->has('ccFiltroResponsableAprobacion')) {
            $filtros['id_responsable_aprobacion'] = session('ccFiltroResponsableAprobacion');
        }
        // $resultado = CuadroCostoView::where($filtros)->where('id_empresa', Auth::user()->id_empresa)->orWhereNull('id_empresa')->select([
        //     'id', 'moneda', 'tipo_cambio', 'id_oportunidad', 'fecha_creacion',
        //     'codigo_oportunidad', 'descripcion_oportunidad', 'fecha_limite', 'nombre_entidad', 'name', 'tipo_cuadro',
        //     'estado_aprobacion', 'nro_orden', 'responsable_aprobacion', 'monto_gg_soles', 'monto_gg_dolares'
        // ]);

        $resultado = CuadroCostoView::where($filtros)->where(function($query) {
            $query->where('id_empresa', Auth::user()->id_empresa)->orWhereNull('id_empresa');
        })->select([
            'id', 'moneda', 'tipo_cambio', 'id_oportunidad', 'fecha_creacion',
            'codigo_oportunidad', 'descripcion_oportunidad', 'fecha_limite', 'nombre_entidad', 'name', 'tipo_cuadro',
            'estado_aprobacion', 'nro_orden', 'responsable_aprobacion', 'monto_gg_soles', 'monto_gg_dolares'
        ]);

        if ($request->session()->has('ccFiltroFechaCreacionDesde')) {
            $resultado = $resultado->whereBetween('fecha_creacion', [Carbon::createFromFormat('d-m-Y', session('ccFiltroFechaCreacionDesde'))->setHour(0)->setMinute(0)->setSecond(0), Carbon::createFromFormat('d-m-Y', session('ccFiltroFechaCreacionHasta'))->setHour(23)->setMinute(59)->setSecond(59)]);
        }
        return $resultado;
    }

    public function getFechaLimiteAttribute()
    {
        return date_format(date_create($this->attributes['fecha_limite']), 'd-m-Y');
    }

    public function getFechaEntregaAttribute()
    {
        return date_format(date_create($this->attributes['fecha_entrega']), 'd-m-Y');
    }

    public function getFechaCreacionAttribute()
    {
        return $this->attributes['fecha_creacion'] == null ? '' : (new Carbon($this->attributes['fecha_creacion']))->format('d-m-Y');
    }

    public function getMargenGananciaAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->margen_ganancia_format;
    }

    public function getFleteTotalAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->flete_total;
    }

    public function getTieneTransformacionAttribute()
    {
        $resultado = DB::selectOne("SELECT CASE WHEN cc.tipo_cuadro=0 THEN 0
        ELSE 
        (SELECT COUNT(*) FROM mgcp_cuadro_costos.cc_am_filas WHERE cc_am_filas.id_cc_am=cc.id AND part_no_producto_transformado IS NOT NULL)
        END AS cantidad
        FROM mgcp_cuadro_costos.cc where id=?", [$this->attributes['id']]);
        return $resultado->cantidad > 0;
    }

    public function getMontoGananciaAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->ganancia_real_format;
    }
}
