<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use App\Models\mgcp\AcuerdoMarco\Proforma\Proforma;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompraOrdinaria extends Proforma
{
    protected $table = 'mgcp_acuerdo_marco.proformas_compra_ordinaria';
    protected $primaryKey = 'nro_proforma';
    public $incrementing = false;

    public static function listar(Request $request)
    {
        $proformas = CompraOrdinaria::with(['producto', 'entidad', 'empresa'])
            ->join('mgcp_acuerdo_marco.empresas', 'empresas.id', '=', 'proformas_compra_ordinaria.id_empresa')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'proformas_compra_ordinaria.id_producto')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'proformas_compra_ordinaria.id_entidad')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', '=', 'productos_am.id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'categorias.id_catalogo')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'catalogos.id_acuerdo_marco', '=', 'acuerdo_marco.id')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_compra_ordinaria.id_ultimo_usuario')
            ->select(['proformas_compra_ordinaria.nro_proforma', 'proformas_compra_ordinaria.id_empresa', 'proforma', 'fecha_emision', 'moneda_ofertada', 'cantidad', 'entidades.nombre AS nombre_entidad',
                'lugar_entrega', 'id_catalogo', 'precio_publicar', 'costo_envio_publicar', 'estado', 'users.nombre_corto', 'users.id AS id_usuario', 'users.name', 'fecha_limite', 'empresas.empresa AS nombre_empresa',
                'software_educativo', 'entidades.ruc', 'requerimiento', 'precio_unitario_base', 'plazo_publicar', 'productos_am.marca', 'productos_am.modelo', 'categorias.descripcion as categoria',
                'productos_am.part_no', 'productos_am.descripcion AS descripcion_producto', 'requiere_flete', 'inicio_entrega', 'fin_entrega', 'id_producto', 'id_entidad','tipo_cambio'
            ]);

        $valores = [];

        if ($request->session()->has('proformaEmpresas')) {
            $proformas = $proformas->whereIn('id_empresa', session('proformaEmpresas'));
        }

        if ($request->session()->has('proformaFechaEmisionDesde')) {
            $proformas = $proformas->whereBetween('fecha_emision', [Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionDesde'))->toDateString(), Carbon::createFromFormat('d-m-Y', session('proformaFechaEmisionHasta'))->toDateString()]);
        }

        if ($request->session()->has('proformaFechaLimiteDesde')) {
            $proformas = $proformas->whereBetween('fecha_limite', [Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteDesde'))->toDateString(), Carbon::createFromFormat('d-m-Y', session('proformaFechaLimiteHasta'))->toDateString()]);
        }

        if ($request->session()->has('proformaEstado')) {
            $valores['estado'] = session('proformaEstado');
        }
        
        if ($request->session()->has('proformaTipoCarga')) {
            $valores['tipo_carga'] = session('proformaTipoCarga');
        }

        if ($request->session()->has('proformaMPG')) {
            $proformas = $proformas->where('probabilidad_ganar', session('proformaMPG'));
        }

        if (count($valores) > 0) {
            $proformas = $proformas->where($valores);
        }

        if ($request->session()->has('proformaCatalogos')) {
            $proformas = $proformas->whereIn('id_catalogo', session('proformaCatalogos'));
        }

        if ($request->session()->has('proformaMarcas')) {
            $proformas = $proformas->whereIn('marca', session('proformaMarcas'));
        }

        if ($request->session()->has('proformaDepartamentos')) {
            $proformas = $proformas->whereIn('id_departamento', session('proformaDepartamentos'));
        }

        return $proformas->where('proformas_compra_ordinaria.id_empresa', Auth::user()->id_empresa);
    }

    public static function generarConsultaRequerimientos(Request $filtros)
    {
        $requerimientosEnProformas = CompraOrdinaria::generarConsultaProformas($filtros)->get();
        $arrayRequerimientos = [];
        foreach ($requerimientosEnProformas as $fila) {
            array_push($arrayRequerimientos, $fila->requerimiento);
        }
        /*Se obtiene la cabecera de los requerimientos. No se utiliza la query anterior porque no es posible paginar el resultado a nivel cabecera, 
        ya que esta trae los detalles*/
        return DB::table('mgcp_acuerdo_marco.proformas_compra_ordinaria')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'id_entidad')
            ->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', '=', 'id_departamento')
            ->select([
                'requerimiento', 'id_entidad', 'entidades.ruc AS ruc_entidad', 'indicador_semaforo', 'entidades.nombre AS entidad', 'lugar_entrega', 'moneda_ofertada', 'departamentos.nombre AS departamento',
                DB::raw('mgcp_acuerdo_marco.proforma_co_monto_requerimiento(requerimiento) AS monto_total')
            ])
            ->distinct()->whereIn('requerimiento', $arrayRequerimientos);
    }

    public static function generarConsultaProformas(Request $filtros)
    {
        $resultado = CompraOrdinaria::with(['empresa'])
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'proformas_compra_ordinaria.id_producto')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', '=', 'productos_am.id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'categorias.id_catalogo')
            ->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', '=', 'proformas_compra_ordinaria.id_departamento')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'proformas_compra_ordinaria.id_entidad')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_compra_ordinaria.id_ultimo_usuario')
            ->whereBetween('proformas_compra_ordinaria.fecha_emision', [Carbon::createFromFormat('d-m-Y', $filtros->fechaEmisionDesde)->toDateString(), Carbon::createFromFormat('d-m-Y', $filtros->fechaEmisionHasta)->toDateString()])
            ->orderBy('proforma')->orderBy('marca')->orderBy('modelo')->orderBy('part_no')->orderBy('id_producto')->orderBy('proformas_compra_ordinaria.id_empresa');

        if (!empty($filtros->criterio)) {
            $criterio = '%' . str_replace(' ', '%', mb_strtoupper($filtros->criterio)) . '%';
            $resultado = $resultado->whereRaw('(entidades.ruc LIKE ? OR requerimiento LIKE ? OR proforma LIKE ? OR entidades.nombre LIKE ? OR productos_am.descripcion LIKE ?)', [$criterio, $criterio, $criterio, $criterio, $criterio]);
        }
        
        if ($filtros->chkEmpresa == 'on') {
            if ($filtros->selectEmpresa != null && count($filtros->selectEmpresa) > 0) {
                $resultado = $resultado->whereIn('id_empresa', $filtros->selectEmpresa);
            } else {
                $resultado = $resultado->whereIn('id_empresa', 0);
            }
        }

        if ($filtros->chkEstado == 'on') {
            $resultado = $resultado->where('estado', $filtros->selectEstado);
        }

        if ($filtros->chkFechaLimite == 'on') {
            $resultado = $resultado->whereBetween('fecha_limite', [Carbon::createFromFormat('d-m-Y', $filtros->fechaLimiteDesde)->toDateString(), Carbon::createFromFormat('d-m-Y', $filtros->fechaLimiteHasta)->toDateString()]);
        }

        if ($filtros->chkCatalogo == 'on') {
            if ($filtros->selectCatalogo != null && count($filtros->selectCatalogo) > 0) {
                $resultado = $resultado->whereIn('id_catalogo', $filtros->selectCatalogo);
            } else {
                $resultado = $resultado->whereIn('id_catalogo', 0);
            }
        }

        if ($filtros->chkDepartamento == 'on') {
            if ($filtros->selectDepartamento != null && count($filtros->selectDepartamento) > 0) {
                $resultado = $resultado->whereIn('id_departamento', $filtros->selectDepartamento);
            } else {
                $resultado = $resultado->whereIn('id_departamento', 0);
            }
        }

        if ($filtros->chkMarca == 'on') {
            if ($filtros->selectMarca != null && count($filtros->selectMarca) > 0) {
                $resultado = $resultado->whereIn('marca', $filtros->selectMarca);
            } else {
                $resultado = $resultado->whereIn('marca', 0);
            }
        }

        if ($filtros->chkTipoCarga == 'on') {
            $resultado = $resultado->where('tipo_carga', $filtros->selectTipoCarga);
        }

        if ($filtros->chkMPG == 'on') {
            $resultado = $resultado->where('probabilidad_ganar', true);
        }

        return $resultado;
    }

    public static function generarConsultaProformasAutomatica($fechaEmisionDesde, $fechaEmisionHasta)
    {
        $resultado = CompraOrdinaria::with(['empresa'])
        ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'proformas_compra_ordinaria.id_producto')
        ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', '=', 'productos_am.id_categoria')
        ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'categorias.id_catalogo')
        ->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', '=', 'proformas_compra_ordinaria.id_departamento')
        ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'proformas_compra_ordinaria.id_entidad')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_compra_ordinaria.id_ultimo_usuario')
            ->whereBetween('fecha_emision', [$fechaEmisionDesde, $fechaEmisionHasta])
            ->orderBy('proforma')->orderBy('marca')->orderBy('modelo')->orderBy('part_no')->orderBy('id_producto')->orderBy('proformas_compra_ordinaria.id_empresa');
        return $resultado;
    }
}
