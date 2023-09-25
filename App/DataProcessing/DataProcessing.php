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

            $arrayContent = json_decode($jsonContent);

            $features = $arrayContent->config->userMap->features;

            foreach ($features as $feature) {
                if (!empty($feature->coordinates)) {
                    $data[] = [
                        'latitude' => $feature->coordinates[1],
                        'longitude' => $feature->coordinates[0],
                        'description' => $feature->subtitle,
                        'label' => $feature->title,
                    ];
                }
            }
        }

        return $data;
    }
}