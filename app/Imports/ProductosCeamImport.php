<?php

namespace App\Imports;

use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\Integracion\ProductoCeam;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class ProductosCeamImport implements ToCollection, WithHeadingRow
{
    private $agregados = 0;
    private $duplicados = 0;

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $consulta = ProductoCeam::where("part_no", $row["part_no"])->count();

            if ($consulta == 0) {
                $producto_am = Producto::where("part_no", $row["part_no"])->count();
                $tipo = ($producto_am > 0) ? "MGC" : "CEAM";

                ProductoCeam::create([
                    "acuerdo_marco" => $row["acuerdo"],
                    "catalogo"      => $row["catalogo"],
                    "categoria"     => $row["categoria"],
                    "producto"      => $row["producto"],
                    "part_no"       => $row["part_no"],
                    "marca"         => $row["marca"],
                    "imagen"        => $row["imagen"],
                    "ficha_tecnica" => $row["ficha"],
                    "estado"        => $row["estado"],
                    "activo"        => true,
                    "tipo"          => $tipo
                ]);
                $this->agregados++;
            } else {
                $this->duplicados++;
            }
        }
    }

    public function getRowCount($tipo): int
    {
        return ($tipo == 1) ? $this->agregados : $this->duplicados;
    }
}
