<?php

namespace App\DataProcessing;

use DOMDocument;
use DOMXPath;

class DataProcessing
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function processData(): array
    {
        $text = file_get_contents($this->url);

        $data = [];

        if (preg_match('/<script.*?type=["\']application\/json["\'].*?>(.*?)<\/script>/is', $text, $matches)) {
            $jsonContent = $matches[1];

            $infoArray = json_encode($jsonContent);

            $mapObjects = $infoArray['config']['userMap']['features'];

            foreach ($mapObjects as $object){
                $data['latitude'] = $object['coordinates'][0];
                $data['longitude'] = $object['coordinates'][1];
                $data['description'] = $object['subtitle'];
                $data['label'] = $object['title'];
            }
        }

        return $data;
    }
}