<?php


namespace App\Helpers\mgcp\Exportar;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class EntidadesProforma
{
    private $libro;
    private $hoja;
    private $estiloTitulo;
    private $estiloCabeceraColumnas;

    function __construct()
    {
        $this->libro = new Spreadsheet();
        $this->libro->getProperties()->setCreator("Módulo de Gestión Comercial")->setDescription('Lista de órdenes de compra propias');
        $this->definitEstilos();
    }

    private function definitEstilos()
    {
        $this->estiloTitulo = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $this->estiloCabeceraColumnas = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                ]
            ]
        ];
    }

    public function exportar($data)
    {
        $this->generarTituloCabecera();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnasCabecera();
        $this->generarContenidoCabecera($data);
        $this->darFormatoContenidoCabecera($data);
        $this->descargarArchivo();
    }

    private function generarTituloCabecera()
    {
        $this->hoja = $this->libro->getActiveSheet();
        $this->hoja->setTitle("Lista");
        $this->generarLogo();
        $fila = 3;
        $this->hoja->setCellValue("A$fila", "Lista de entidades por proformas ordinarias");

        $this->hoja->getStyle("A$fila")->applyFromArray($this->estiloTitulo);
        $this->hoja->mergeCells("A$fila:H$fila");
        //Cabecera de columnas
        $fila = 6;
        $this->hoja->getRowDimension($fila)->setRowHeight(32);
        $this->hoja->setCellValue("A$fila", "RUC");
        $this->hoja->setCellValue("B$fila", "Razón social");
        $this->hoja->setCellValue("C$fila", "Dirección");
        $this->hoja->setCellValue("D$fila", "Ubigeo");
        $this->hoja->setCellValue("E$fila", "Responsable");
        $this->hoja->setCellValue("F$fila", "Cargo");
        $this->hoja->setCellValue("G$fila", "Correo");
        $this->hoja->setCellValue("H$fila", "Teléfono");
        $this->hoja->getStyle("A$fila:H$fila")->applyFromArray($this->estiloCabeceraColumnas)->getAlignment()->setWrapText(true);
    }

    private function generarFechaExportacion()
    {
        $fila = 5;
        $this->hoja->setCellValue("A$fila", "Generado el " . (new Carbon())->format("d/m/Y"));
    }

    private function establecerAnchoColumnasCabecera()
    {
        $this->hoja->getColumnDimension('A')->setWidth(20);
        $this->hoja->getColumnDimension('B')->setWidth(30);
        $this->hoja->getColumnDimension('C')->setWidth(25);
        $this->hoja->getColumnDimension('D')->setWidth(15);
        $this->hoja->getColumnDimension('E')->setWidth(21);
        $this->hoja->getColumnDimension('F')->setWidth(25);
        $this->hoja->getColumnDimension('G')->setWidth(25);
        $this->hoja->getColumnDimension('H')->setWidth(15);
        
    }

    private function generarContenidoCabecera($data)
    {
        $fila = 7;
        foreach ($data as $proforma) {
            $this->hoja->setCellValue("A$fila", $proforma->entidad->ruc);
            $this->hoja->setCellValue("B$fila", $proforma->entidad->nombre);
            $this->hoja->setCellValue("C$fila", $proforma->entidad->direccion);
            $this->hoja->setCellValue("D$fila", $proforma->entidad->ubigeo);
            $this->hoja->setCellValue("E$fila", $proforma->entidad->responsable);
            $this->hoja->setCellValue("F$fila", $proforma->entidad->cargo);
            $this->hoja->setCellValue("G$fila", $proforma->entidad->correo);
            $this->hoja->setCellValue("H$fila", $proforma->entidad->telefono);
            $fila++;
        }
    }

    private function darFormatoContenidoCabecera($data)
    {
        $filaInicial = 7;
        $totalFilas = $filaInicial + $data->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $arrayIzquierda = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $this->hoja->getStyle("A$filaInicial:A$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("B$filaInicial:B$totalFilas")->applyFromArray($arrayIzquierda);
        $this->hoja->getStyle("C$filaInicial:C$totalFilas")->applyFromArray($arrayIzquierda);
        $this->hoja->getStyle("D$filaInicial:D$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("E$filaInicial:E$totalFilas")->applyFromArray($arrayIzquierda);
        $this->hoja->getStyle("F$filaInicial:F$totalFilas")->applyFromArray($arrayIzquierda);
        $this->hoja->getStyle("G$filaInicial:G$totalFilas")->applyFromArray($arrayIzquierda);
        $this->hoja->getStyle("H$filaInicial:H$totalFilas")->applyFromArray($arrayCentrar);
    }

    private function generarLogo()
    {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/mgcp/img/logo.png');
        $drawing->setHeight(65);
        $drawing->setWorksheet($this->hoja);
    }

    public function descargarArchivo()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="EntidadesProformas.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->libro);
        $writer = IOFactory::createWriter($this->libro, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}