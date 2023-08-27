<?php

require 'vendor/autoload.php';

use App\DataProcessing\DataProcessing;
use App\SpreadsheetWriter\SpreadsheetWriter;

$urls = [
    'https://yandex.ru/maps/?l=trf%2Ctrfe&ll=47.900923%2C56.834995&mode=usermaps&source=constructorLink&um=constructor%3ASsAjXaRatZfZ38krbYkBXdv5Z7l5G1Wk&z=4.8',
    'https://yandex.ru/maps/?ll=37.683354%2C55.709483&mode=usermaps&source=constructorLink&um=constructor%3Af7c6ce117b56702414f46aac65e0b7f8f2f9e2a0b2b9fd4023df298581ee0916&z=9',
    'https://yandex.ru/maps/213/moscow/?ll=37.617698%2C55.755864&mode=usermaps&um=constructor%3A13605636e05724b5e160cc11ded29c5da9b2fda18fc9f3f4c266d66e2d07d2e1&z=13',
    'https://yandex.ru/maps/?ll=37.866504%2C55.578908&mode=usermaps&source=constructorLink&um=constructor%3A0a9e384ef9f825772566fe155b13d147daa402d834f553581653b9cef87beb36&z=9'
];

$writer = new SpreadsheetWriter();

foreach ($urls as $url) {
    $processor = new DataProcessing($url);
    $data = $processor->processData();
    $writer->writeToSpreadsheet($data);
}

try {
    $writer->saveToFile(__DIR__ . '/output/parsed.xlsx');
} catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
    echo $e->getMessage();
}