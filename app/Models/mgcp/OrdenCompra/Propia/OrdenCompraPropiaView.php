<?php

namespace App\Models\mgcp\OrdenCompra\Propia;

use App\Models\Gerencial\Penalidad;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Contacto;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\ComentarioOcAm;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\ComentarioOcDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OrdenCompraPropiaView extends Model
{

    protected $table = 'mgcp_ordenes_compra.oc_propias_view';
    public $timestamps = false;
    protected $appends = ['fecha_estado_format', 'fecha_guia_format', 'fecha_entrega_format', 'inicio_entrega_format', 'fecha_publicacion_format', 'monto_total_format', 'fecha_aprobacion'];

    public static function listar($request, $incluirCc = false)
    {
        if ($incluirCc) {
            $ordenes = OrdenCompraPropiaView::with(['entidad', 'oportunidad', 'oportunidad.cuadroCosto'])->select(['*']);
        } else {
            $ordenes = OrdenCompraPropiaView::with('entidad')->select(['*']);
        }
        $valores = [];

        if ($request->session()->has('ocFiltroEstadoOc')) {
            $ordenes = $ordenes->whereIn("estado_oc", session('ocFiltroEstadoOc'));
        }

        if ($request->session()->has('ocFiltroEstadoCuadro')) {
            $valores['id_estado_aprobacion'] = session('ocFiltroEstadoCuadro');
        }

        if ($request->session()->has('ocFiltroEstadoEntrega')) {
            $valores['estado_entrega'] = session('ocFiltroEstadoEntrega');
        }

        if ($request->session()->has('ocFiltroEtapaAdq')) {
            $valores['id_etapa'] = session('ocFiltroEtapaAdq');
        }

        if ($request->session()->has('ocFiltroTipo')) {
            $valores['id_tipo'] = session('ocFiltroTipo') == 0 ? null : session('ocFiltroTipo');
        }

        if ($request->session()->has('ocFiltroEntidad')) {
            $valores['id_entidad'] = session('ocFiltroEntidad');
        }

        if ($request->session()->has('ocFiltroConformidad')) {
            $valores['conformidad'] = session('ocFiltroConformidad');
        }

        if ($request->session()->has('ocFiltroCobrado')) {
            $valores['cobrado'] = session('ocFiltroCobrado');
        }

        if (Auth::user()->tieneRol(48)) {
            $valores['id_responsable_oc'] = Auth::user()->id;
        } else {
            if ($request->session()->has('ocFiltroCorporativo') && is_numeric(session('ocFiltroCorporativo'))) {
                $valores['id_responsable_oc'] = session('ocFiltroCorporativo');
            }
        }

        if ($request->session()->has('ocFiltroFechaEstadoDesde')) {
            $ordenes = $ordenes->whereBetween('fecha_estado', [Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaEstadoDesde'))->setHour(0)->setMinute(0)->setSecond(0), Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaEstadoHasta'))->setHour(23)->setMinute(59)->setSecond(59)]);
        }

        if ($request->session()->has('ocFiltroFechaEntregaDesde')) {
            $ordenes = $ordenes->whereBetween('fecha_entrega', [Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaEntregaDesde'))->setHour(0)->setMinute(0)->setSecond(0), Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaEntregaHasta'))->setHour(23)->setMinute(59)->setSecond(59)]);
        }

        if ($request->session()->has('ocFiltroFechaPublicacionDesde')) {
            $ordenes = $ordenes->whereBetween('fecha_publicacion', [Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaPublicacionDesde'))->setHour(0)->setMinute(0)->setSecond(0), Carbon::createFromFormat('d-m-Y', session('ocFiltroFechaPublicacionHasta'))->setHour(23)->setMinute(59)->setSecond(59)]);
        }

        if ($request->session()->has('ocFiltroAm')) {
            $ordenes = $ordenes->whereIn("oc_propias_view.id_acuerdo_marco", session('ocFiltroAm'));
        }

        if ($request->session()->has('ocFiltroEmpresa')) {
            $ordenes = $ordenes->whereIn("id_empresa", session('ocFiltroEmpresa'));
        }

        if ($request->session()->has('ocFiltroMarca')) {
            $ordenes = $ordenes->whereRaw("oc_propias_view.id IN 
            (SELECT oc_propias.id FROM mgcp_acuerdo_marco.oc_propias 
            INNER JOIN mgcp_acuerdo_marco.oc_publica_detalles ON oc_publica_detalles.id_orden_compra=oc_propias.id
            INNER JOIN mgcp_acuerdo_marco.productos_am ON productos_am.id=id_producto WHERE marca IN ('" . implode("','", session('ocFiltroMarca')) . "') )");
        }

        if ($request->session()->has('ocFiltroSinCuadro')) {
            $ordenes = $ordenes->whereNull("id_oportunidad");
        }

        if ($request->session()->has('ocFiltroSolAprob24h')) {
            $valores['sol_aprob_despues_24h'] = true;
        }

        if ($request->session()->has('ocFiltroFlagEstado')) {
            switch (session('ocFiltroFlagEstado')) {
                case 'Blanco':
                    // $valores['id_responsable_oc'] = null;
                    $ordenes = $ordenes->whereNull("id_responsable_oc");
                break;
                case 'Amarillo-1':
                    $ordenes = $ordenes->whereNull("id_oportunidad");
                break;
                case 'Amarillo-2':
                    $ordenes = $ordenes->where('estado_aprobacion_cuadro', 'Inicial');
                break;
                case 'Naranja-1':
                    $ordenes = $ordenes->where('estado_aprobacion_cuadro', 'Aprobación pendiente');
                break;
                case 'Naranja-2':
                    $ordenes = $ordenes->where('estado_aprobacion_cuadro', 'Aprobado - pendiente de regularización');
                break;
                case 'Azul':
                    $ordenes = $ordenes->where('estado_aprobacion_cuadro', 'Aprobado - etapa de compras');
                break;
                case 'Verde':
                    $ordenes = $ordenes->where('estado_aprobacion_cuadro', 'Finalizado');
                break;
            }
        }

        if (count($valores) > 0) {
            $ordenes = $ordenes->where($valores);
        }
        return $ordenes->where('id_empresa', Auth::user()->id_empresa); //$ordenes->where($valores);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'id_etapa');
    }

    public function corporativo()
    {
        return $this->belongsTo(User::class, 'id_responsable_oc')->withTrashed();
    }

    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'id_contacto');
    }

    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class, 'id_oportunidad');
    }

    public function ultimoComentario()
    {
        if ($this->attributes['tipo'] == 'am') {
            return ComentarioOcAm::where('id_oc', $this->attributes['id'])->orderBy('fecha', 'desc')->first();
        } else {
            return ComentarioOcDirecta::where('id_oc', $this->attributes['id'])->orderBy('fecha', 'desc')->first();
        }
    }

    public function setLugarEntregaAttribute($value)
    {
        $this->attributes['lugar_entrega'] = str_replace(" -", " - ", str_replace("  ", "", $value));
    }

    public function getLugarEntregaAttribute()
    {
        return html_entity_decode($this->attributes['lugar_entrega']);
    }

    public function getFacturaAttribute()
    {
        return ($this->attributes['factura'] == null ? '' : $this->attributes['factura']);
    }

    public function getOccAttribute()
    {
        return ($this->attributes['occ'] == null ? '' : $this->attributes['occ']);
    }

    public function getArchivosAttribute()
    {
        $archivos = '<ul class="list-unstyled">';
        if ($this->attributes['tipo'] == 'am') {
            $orden = OrdenCompraAm::find($this->attributes['id']);
            $archivos .= "<li style='margin-bottom: 5px'><a target='_blank' href='https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=" . $this->attributes['id'] . "&ImprimirCompleto=1'>O/C digital</a></li>";
            $archivos .= "<li><a target='_blank' href='$orden->url_oc_fisica'>O/C física</a></li>";
        } else {
            $path = storage_path('app/mgcp/ordenes-compra/directas/' . $this->attributes['id']);
            if (!is_dir($path)) {
                $archivos .= '<li style="margin-bottom: 5px">Sin archivos</li>';
            } else {
                $files = File::files($path);
                foreach ($files as $file) {
                    $archivos .= '<li style="margin-bottom: 5px"><a target="_blank" href="' . route('mgcp.ordenes-compra.propias.directas.descargar-archivo', ['id' => $this->attributes['id'], 'archivo' => $file->getFilename()]) . '">' . $file->getFilename() . '</a></li>';
                }
            }
        }
        $archivos .= '</ul>';
        return $archivos;
    }

    public function getGuiaAttribute()
    {
        return ($this->attributes['guia'] == null ? '' : $this->attributes['guia']);
    }

    public function getOrdenCompraAttribute()
    {
        return ($this->attributes['orden_compra'] == null ? '' : $this->attributes['orden_compra']);
    }

    public function getSiafAttribute()
    {
        return ($this->attributes['siaf'] == null ? '' : $this->attributes['siaf']);
    }

    public function getMontoTotalFormatAttribute()
    {
        return ($this->attributes['moneda_oc'] == 's' ? 'S/ ' : '$ ') . number_format($this->attributes['monto_total'], 2, '.', ',');
    }

    public function setOccAttribute($valor)
    {
        if ($valor == '' || $valor == null) {
            $this->attributes['occ'] = null;
        } else {
            $this->attributes['occ'] = mb_strtoupper($valor);
        }
    }

    public function setFechaEntregaAttribute($valor)
    {
        if ($valor == '' || $valor == null) {
            $this->attributes['fecha_entrega'] = null;
        } else {
            $this->attributes['fecha_entrega'] = Carbon::createFromFormat('d/m/Y', $valor)->toDateString();
        }
    }

    public function getFechaEstadoFormatAttribute()
    {
        return $this->attributes['fecha_estado'] == null ? '' : date_format(date_create($this->attributes['fecha_estado']), 'd-m-Y H:i');
    }

    public function getFechaAprobacionAttribute()
    {
        return $this->attributes['fecha_aprobacion'] == null ? '' : (new Carbon($this->attributes['fecha_aprobacion']))->format('d-m-Y'); //date_format(date_create($this->attributes['fecha_aprobacion']), 'd-m-Y H:i');
    }

    public function getFechaGuiaFormatAttribute()
    {
        if ($this->attributes['fecha_guia'] == null) {
            return '';
        } else {
            return date_format(date_create($this->attributes['fecha_guia']), 'd-m-Y');
        }
    }

    public function getFechaEntregaFormatAttribute()
    {
        if ($this->attributes['fecha_entrega'] == null) {
            return '';
        } else {
            return date_format(date_create($this->attributes['fecha_entrega']), 'd-m-Y');
        }
    }

    public function getInicioEntregaFormatAttribute()
    {
        if ($this->attributes['inicio_entrega'] == null) {
            return '';
        } else {
            return date_format(date_create($this->attributes['inicio_entrega']), 'd-m-Y');
        }
    }

    public function getFechaPublicacionFormatAttribute()
    {
        return ($this->attributes['fecha_publicacion'] == null ? null : date_format(date_create($this->attributes['fecha_publicacion']), 'd-m-Y'));
    }

    public function setFechaGuiaAttribute($valor)
    {
        if ($valor == '' || $valor == null) {
            $this->attributes['fecha_guia'] = null;
        } else {
            $this->attributes['fecha_guia'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
        }
    }

    public static function actualizarEstadoCompra($ocPropiaView, $estado)
    {
        if (!is_null($ocPropiaView)) {
            $ordenCompra = $ocPropiaView->tipo == 'directa' ? OrdenCompraDirecta::find($ocPropiaView->id) : OrdenCompraAm::find($ocPropiaView->id);
            $ordenCompra->id_etapa = $estado;
            $ordenCompra->save();
        }
    }

    public static function obtenerMontoMensual($mes, $anio)
    {
        return OrdenCompraPropiaView::where('id_empresa', Auth::user()->id_empresa)->whereRaw("
        (tipo = 'am' AND estado_oc = 'ACEPTADA' AND estado_entrega NOT LIKE 'RESUELTA%' AND EXTRACT(MONTH FROM fecha_estado) = ? AND EXTRACT(YEAR FROM fecha_estado) = ?)
        OR (tipo = 'directa' AND EXTRACT(MONTH FROM fecha_publicacion) = ? AND EXTRACT(YEAR FROM fecha_publicacion) = ?) OR (tipo= 'am' AND estado_oc != 'RESUELTA' AND 
        id IN (SELECT id_oc FROM mgcp_acuerdo_marco.oc_propias_estados WHERE id_oc = oc_propias_view.id AND EXTRACT(MONTH FROM fecha) = ? AND EXTRACT(YEAR FROM fecha) = ? AND estado LIKE 'ACEPTADA%') AND
        id NOT IN (SELECT id_oc FROM mgcp_acuerdo_marco.oc_propias_estados WHERE id_oc = oc_propias_view.id AND EXTRACT(MONTH FROM fecha) < ? AND EXTRACT(YEAR FROM fecha) = ? AND estado LIKE 'ACEPTADA%'))",
        [$mes, $anio, $mes, $anio, $mes, $anio, $mes, $anio])->sum('monto_soles');
    }
}
