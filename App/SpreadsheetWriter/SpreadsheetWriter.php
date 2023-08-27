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
        $j = 2; // Starting row index for data

        foreach ($data as $datum) {
            $this->activeWorksheet->setCellValue('A' . $j, $datum['title']);
            $this->activeWorksheet->setCellValue('B' . $j, $datum['description']);
            $this->activeWorksheet->setCellValue('C' . $j, $datum['phone_numbers']);
            $this->activeWorksheet->setCellValue('D' . $j, $datum['email_addresses']);
            $this->activeWorksheet->setCellValue('E' . $j, $datum['source']); // Добавляем ссылку в ячейку E
            $j++;
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

