<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();

$url = 'https://yandex.ru/maps/?l=trf%2Ctrfe&ll=50.038923%2C50.002621&mode=usermaps&source=constructorLink&um=constructor%3ASsAjXaRatZfZ38krbYkBXdv5Z7l5G1Wk&z=3.14';

$dom = new DOMDocument();

libxml_use_internal_errors(true);
if (!$dom->loadHTMLFile($url)) {
    echo "Failed to load the page";
    exit;
}
libxml_use_internal_errors(false);

$xpath = new DOMXPath($dom);

$elements = $xpath->query('//div[contains(@class, "user-maps-features-view__text")]');

$data = [];

$i = 1;
$j = 1;
foreach ($elements as $element) {
    $titleElement = $xpath->query('.//span', $element)->item(0);
    $title = $titleElement ? $titleElement->nodeValue : '';

    $description = '';
    foreach ($element->childNodes as $childNode) {
        if ($childNode->nodeName === 'span') {
            continue;
        }
        $description .= $dom->saveHTML($childNode);
    }

    $data[] = [
        'title' => $title,
        'description' => $description
    ];
}

foreach ($data as $datum){
    $activeWorksheet->setCellValue('A'. $i++, $datum['title']);
    $activeWorksheet->setCellValue('B'. $j++, $datum['description']);
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . 'parsed.xlsx');