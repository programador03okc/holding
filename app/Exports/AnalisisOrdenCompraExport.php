<?php

namespace App\Exports;

use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaAnalisis;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalisisOrdenCompraExport implements FromView, WithColumnFormatting, WithStyles
{

    public function view() : View
    {
        $data = [];
        $query = OrdenCompraPublicaAnalisis::select('oc_publicas_analisis.*', 'oc_publicas_analisis.id AS id_ocp', 'entidades.nombre AS entidad', 'empresas.empresa AS empresa', 'productos_am.*', 'oc_publicas_proveedores.nombre AS proveedor')
        ->join('mgcp_acuerdo_marco.entidades', 'oc_publicas_analisis.id_entidad', '=', 'entidades.id')
        ->leftJoin('mgcp_acuerdo_marco.empresas', 'oc_publicas_analisis.id_empresa', '=', 'empresas.id')
        ->leftJoin('mgcp_acuerdo_marco.productos_am', 'oc_publicas_analisis.id_producto', '=', 'productos_am.id')
        ->leftJoin('mgcp_acuerdo_marco.oc_publicas_proveedores', 'oc_publicas_analisis.id_proveedor', '=', 'oc_publicas_proveedores.id');

        if (session()->has('ocpAnalisisFechaDesde')) {
            $query = $query->whereBetween('oc_publicas_analisis.fecha', [session()->get('ocpAnalisisFechaDesde'), session()->get('ocpAnalisisFechaHasta')]);
        }
        
        if (session()->has('ocpAnalisisEmpresa')) {
            if (session()->has('ocpAnalisisEmpresa') != null) {
                $query = $query->whereIn('id_empresa', session()->get('ocpAnalisisEmpresa'));
            } else {
                $query = $query->where('id_empresa', '>', 0);
            }
        }

        if (session()->has('ocpAnalisisEntidad')) {
            $query = $query->whereIn('id_entidad', session()->get('ocpAnalisisEntidad'));
        }

        if (session()->has('ocpAnalisisProveedor')) {
            $query = $query->whereIn('id_proveedor', session()->get('ocpAnalisisProveedor'));
        }

        if (session()->has('ocpAnalisisMarca')) {
            $txtMarca = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisMarca'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtMarca]);
        }

        if (session()->has('ocpAnalisisModelo')) {
            $txtModelo = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisModelo'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtModelo]);
        }

        if (session()->has('ocpAnalisisProcesador')) {
            $txtProcesador = '%'.str_replace(' ', '%', mb_strtoupper(session()->get('ocpAnalisisProcesador'))).'%';
            $query = $query->whereRaw('(productos_am.descripcion LIKE ?)', [$txtProcesador]);
        }

        foreach ($query->orderBy('fecha', 'desc')->get() as $key) {
            $producto = '';
            $producto_ext = '';
            $proveedor = '';
            if ($key->id_producto != null) {
                $proInt = Producto::where('id', $key->id_producto)->first();
                $producto = $proInt->descripcion;
            }

            if ($key->id_producto_ext != null) {
                $proExt = Producto::where('id', $key->id_producto_ext)->first();
                $producto_ext = $proExt->descripcion;
                if ($key->id_proveedor != null) {
                    $proveedor = $key->proveedor;
                }
            }

            $data[] = [
                'fecha'         => $key->fecha,
                'entidad'       => $key->entidad,
                'cantidad'      => $key->cantidad,
                'empresa'       => $key->empresa,
                'producto'      => $producto,
                'costo'         => $key->precio_costo,
                'precio_uni'    => $key->precio_dolares,
                'total'         => $key->total,
                'margen'        => $key->margen,
                'proveedor'     => $proveedor,
                'producto_ext'  => $producto_ext,
                'costo_ext'     => $key->precio_costo_ext,
                'precio_uni_ext'=> $key->precio_dolares_ext,
                'total_ext'     => $key->total_ext,
                'margen_ext'    => $key->margen_ext,
            ];
        }

        return view('mgcp.orden-compra.publica.exportar.reporte-analisis-orden-compra', ['data' => $data]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B2:B'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('D2:D'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('E2:E'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('J2:J'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('K2:K'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:O')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => array('rgb' => '000000'),
                ],
            ],
        ];
        
        $sheet->getStyle('A1:O2')->applyFromArray($styleArray);

        return [
            1    => ['font' => ['bold' => true] ],
            2    => ['font' => ['bold' => true] ],
            'A:O'  => ['font' => ['size' => 10]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
