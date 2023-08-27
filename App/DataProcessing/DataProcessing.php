<?php

namespace App\DataProcessing;

use DOMDocument;
use DOMXPath;

class DataProcessing
{
    private string $url;
    private DOMDocument $dom;
    private DOMXPath $xpath;
    private array $processedPhoneNumbers = [];

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if (!$this->dom->loadHTMLFile($this->url)) {
            echo "Failed to load the page: $this->url\n";
            return;
        }
        libxml_use_internal_errors(false);
        $this->xpath = new DOMXPath($this->dom);
    }

    public function processData(): array
    {
        $elements = $this->xpath->query('//div[contains(@class, "user-maps-features-view__text")]');
        $data = [];

        foreach ($elements as $element) {
            $titleElement = $this->xpath->query('.//span', $element)->item(0);
            $title = $titleElement ? $titleElement->nodeValue : '';

            $description = '';
            $phones = [];
            $emails = [];

            foreach ($element->childNodes as $childNode) {
                if ($childNode->nodeName === 'span') {
                    continue;
                }

                $text = trim($childNode->textContent);
                $description .= $this->dom->saveHTML($childNode);

                // Extract phone numbers using the given pattern
                preg_match_all('/(?:\+?7|8)?\s?\(?\d{3}\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}/', $text, $phoneMatches);

                foreach ($phoneMatches[0] as $phoneNumber) {
                    $phoneData = [
                        'number' => $phoneNumber,
                        'status' => in_array($phoneNumber, $this->processedPhoneNumbers) ? 'дубл.' : '',
                    ];
                    $phones[] = $phoneData;
                    $this->processedPhoneNumbers[] = $phoneNumber;
                }

                // Extract email addresses using the given pattern
                preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $emailMatches);
                $emails = array_merge($emails, $emailMatches[0]);
            }

            $data[] = [
                'title' => $title,
                'description' => $description,
                'phone_numbers' => implode(', ', array_column($phones, 'number')), // Преобразуем массив в строку
                'email_addresses' => implode(', ', $emails),
                'source' => $this->url, // Добавляем ссылку в массив данных
            ];
        }

        return $data;
    }
}