<?php


namespace App\Helpers\mgcp;


use App\Models\User;
use Illuminate\Support\Facades\DB;

class OportunidadResumenHelper
{
    public static function obtenerResponsables($empresa)
    {
        return User::whereRaw('id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado = false AND id_empresa = (?))')->orderBy('name', 'asc')->setBindings([$empresa])->get();
    }

    public static function obtenerSumaImportes($empresa)
    {
        return DB::select("SELECT e.id, estado, COUNT(id_estado) AS cantidad,
            SUM(CASE WHEN moneda = 's' THEN importe ELSE 0 END) AS suma_soles,
            SUM(CASE WHEN moneda = 'd' THEN importe ELSE 0 END) AS suma_dolares
            FROM mgcp_oportunidades.estados AS e
            LEFT JOIN mgcp_oportunidades.oportunidades AS o ON o.id_estado = e.id AND eliminado = false AND id_empresa = (?)
            GROUP BY e.id, estado
            ORDER BY e.id ASC", [$empresa]
        );
    }

    public static function obtenerSumaImportesResponsable($responsable, $empresa)
    {
        return DB::select("SELECT e.id, estado, COUNT(estado) AS cantidad,
            SUM(CASE WHEN moneda = 's' THEN importe ELSE 0 END) AS suma_soles,
            SUM(CASE WHEN moneda = 'd' THEN importe ELSE 0 END) AS suma_dolares
            FROM mgcp_oportunidades.estados AS e
            LEFT JOIN mgcp_oportunidades.oportunidades AS o ON o.id_estado = e.id AND eliminado = false AND id_empresa = (?)
            AND id_responsable = (?)
            GROUP BY e.id, estado
            ORDER BY e.id ASC", [$empresa, $responsable->id]
        );
    }

    public static function obtenerSumaImporteEstadoResponsable($estado, $responsable, $empresa)
    {
        return DB::select("SELECT
            SUM(CASE WHEN moneda='s' THEN importe ELSE 0 END) AS suma_soles,
            SUM(CASE WHEN moneda='d' THEN importe ELSE 0 END) AS suma_dolares
            FROM mgcp_oportunidades.oportunidades
            WHERE eliminado = false AND id_empresa = (?) AND id_estado IN (" . $estado . ")
            AND id_responsable = (?)", [$empresa, $responsable->id]
        );
    }
}
