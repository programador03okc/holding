<?php

namespace App\Exports;

use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProducto;
use App\Models\mgcp\AcuerdoMarco\Proforma\CalculadoraProductoDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\ComentarioCompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\AcuerdoMarco\Proforma\ProformaAnalisis;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormatoProformaExport implements FromView, WithColumnFormatting, WithStyles
{
    public $proformas, $tipoProforma;

    public function __construct($proformas, $tipoProforma)
    {
        $this->proformas = $proformas;
        $this->tipoProforma = $tipoProforma;
    }

    public function view() : View
    {
        if ($this->tipoProforma == 1) {
            $query = CompraOrdinaria::select(['nro_proforma', 'proforma', 'fecha_emision', 'fecha_limite', 'inicio_entrega', 'fin_entrega', 'marca', 'modelo', 
                'part_no', 'id_entidad', 'proformas_compra_ordinaria.id_empresa', 'id_producto', 'software_educativo', 'cantidad', 'precio_unitario_base', 'moneda_ofertada', 'id_ultimo_usuario',
                'estado', 'costo_envio_publicar', 'proforma', 'empresas.empresa AS nombre_empresa', 'entidades.nombre AS nombre_entidad', 'categorias.descripcion AS categoria', 
                'requiere_flete', 'precio_publicar', 'plazo_publicar','users.nombre_corto AS nombre_usuario'
            ])
            ->join('mgcp_acuerdo_marco.empresas', 'proformas_compra_ordinaria.id_empresa', '=', 'empresas.id')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', 'id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', 'id_catalogo')
            ->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', 'id_departamento')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', 'id_entidad')
            ->leftJoin('mgcp_usuarios.users', 'id_ultimo_usuario', 'users.id')
            ->whereIn('nro_proforma', $this->proformas)->get();
        } else {
            $query = GranCompra::select(['nro_proforma', 'proforma', 'fecha_emision', 'fecha_limite', 'inicio_entrega', 'fin_entrega', 'marca', 'modelo', 
                'part_no', 'id_entidad', 'proformas_gran_compra.id_empresa', 'id_producto', 'software_educativo', 'cantidad', 'precio_unitario_base', 'moneda_ofertada', 'id_ultimo_usuario',
                'estado', 'costo_envio_publicar', 'proforma', 'empresas.empresa AS nombre_empresa', 'entidades.nombre AS nombre_entidad', 'categorias.descripcion AS categoria', 
                'requiere_flete', 'precio_publicar', 'plazo_publicar','users.nombre_corto AS nombre_usuario'
            ])
            ->join('mgcp_acuerdo_marco.empresas', 'proformas_gran_compra.id_empresa', '=', 'empresas.id')
            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', '=', 'id_producto')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'id_entidad')
            ->join('mgcp_acuerdo_marco.categorias', 'productos_am.id_categoria', '=', 'categorias.id')
            ->join('mgcp_acuerdo_marco.catalogos', 'id_catalogo', '=', 'catalogos.id')
            ->join('mgcp_acuerdo_marco.acuerdo_marco', 'id_acuerdo_marco', '=', 'acuerdo_marco.id')
            ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'proformas_gran_compra.id_ultimo_usuario')
            ->whereIn('nro_proforma', $this->proformas)->get(); 
        }

        $data = [];
        foreach ($query as $key) {
            $costo = CalculadoraProductoDetalle::where('nro_proforma', $key->nro_proforma)->where('tipo_proforma', 1)->sum('monto');
            $flete = CalculadoraProducto::where('nro_proforma', $key->nro_proforma)->where('tipo_proforma', 1)->sum('flete');
            $precio = (($key->precio_publicar + $flete) > 0) ? ($key->precio_publicar + $flete) : 0;
            $total = $precio * $key->cantidad;
                
            $proveedor = '';
            $marca = '';
            $part_no = '';
            $modelo = '';
            $costo_com = 0;
            $precio_com = 0;
            $margen_com = 0;
            $dataAnalisis = ProformaAnalisis::where('id_proforma', $key->nro_proforma);

            if ($dataAnalisis->count() > 0) {
                $proveedor = ($dataAnalisis->first()->id_proveedor != null) ? $dataAnalisis->first()->proveedor->nombre : '';
                $marca = $dataAnalisis->first()->producto->marca;
                $part_no = $dataAnalisis->first()->producto->part_no;
                $modelo = $dataAnalisis->first()->producto->modelo;
                $costo_com = $dataAnalisis->first()->precio_costo;
                $precio_com = $dataAnalisis->first()->precio_dolares;
                $margen_com = $dataAnalisis->first()->margen;
            }

            if ($this->tipoProforma == 1) {
                $comentarios = ComentarioCompraOrdinaria::where('id_proforma', $key->nro_proforma)->get();
            } else {
                $comentarios = [];
            }

            $data[] = [
                'nombre_entidad'        => $key->nombre_entidad,
                'marca'                 => $key->marca,
                'modelo'                => $key->modelo,
                'part_no'               => $key->part_no,
                'categoria'             => $key->categoria,
                'proforma'              => $key->proforma,
                'nro_proforma'          => $key->nro_proforma,
                'cantidad'              => $key->cantidad,
                'fecha_emision'         => $key->fecha_emision,
                'fin_entrega'           => $key->fin_entrega,
                'costo'                 => $costo,
                'precio_unitario_base'  => $key->precio_publicar,
                'precio_flete'          => $precio,
                'total'                 => $total,
                'margen'                => 0,
                'resultado'             => $key->estado,
                'comp_proveedor'        => $proveedor,
                'comp_marca'            => $marca,
                'comp_part_no'          => $part_no,
                'comp_modelo'           => $modelo,
                'comp_costo'            => $costo_com,
                'comp_precio'           => $precio_com,
                'comp_margen'           => $margen_com,
                'comentarios'           => $comentarios
            ];
        }

        return view('mgcp.acuerdo-marco.proforma.exportar-proforma-analisis', ['lista' => $data]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C2:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('F2:F' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('G2:G' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:V')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
