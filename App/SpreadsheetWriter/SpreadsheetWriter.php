<?php

namespace App\SpreadsheetWriter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadsheetWriter
{
    private Spreadsheet $spreadsheet;
    private Worksheet $activeWorksheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->activeWorksheet = $this->spreadsheet->getActiveSheet();
    }

    public function writeToSpreadsheet(array $data): void
    {
        if (!empty($data)){
            $i = 1;
            foreach ($data as $item) {
                $this->activeWorksheet->setCellValue('A'.$i, $item['latitude']);
                $this->activeWorksheet->setCellValue('B'.$i, $item['longitude']);
                $this->activeWorksheet->setCellValue('C'.$i, $item['description']);
                $this->activeWorksheet->setCellValue('D'.$i, $item['label']);
                $i++;
            }
        }
    }

    /**
     * @throws Exception
     */
    public function saveToFile(string $fileName): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($fileName);
    }
}

