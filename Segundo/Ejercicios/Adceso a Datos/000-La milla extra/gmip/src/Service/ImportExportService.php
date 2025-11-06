<?php

declare(strict_types=1);

namespace GMIP\Service;

use SimpleXMLElement;

class ImportExportService
{
    public static function toCSV(array $rows, string $separator = ','): string
    {
        if (empty($rows)) return '';
        $headers = array_keys($rows[0]);
        $out = implode($separator, $headers) . "\n";
        foreach ($rows as $r) {
            $line = [];
            foreach ($headers as $h) {
                $val = isset($r[$h]) ? (string)$r[$h] : '';
                // Escapar comillas
                $line[] = '"' . str_replace('"', '""', $val) . '"';
            }
            $out .= implode($separator, $line) . "\n";
        }
        return $out;
    }

    public static function fromCSV(string $csv, string $separator = ','): array
    {
        $lines = preg_split('/\r?\n/', trim($csv));
        if (!$lines || count($lines) < 2) return [];
        $headers = str_getcsv($lines[0], $separator);
        $result = [];
        for ($i = 1; $i < count($lines); $i++) {
            $vals = str_getcsv($lines[$i], $separator);
            if (count($vals) !== count($headers)) continue;
            $row = [];
            foreach ($headers as $idx => $h) {
                $row[$h] = $vals[$idx];
            }
            $result[] = $row;
        }
        return $result;
    }

    public static function toJSON(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function fromJSON(string $json): array
    {
        $data = json_decode($json, true);
        if (!is_array($data)) return [];
        return $data;
    }

    public static function toXML(array $data, string $rootName = 'items'): string
    {
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$rootName}/>");
        foreach ($data as $item) {
            $node = $xml->addChild('item');
            foreach ($item as $k => $v) {
                $node->addChild((string)$k, htmlspecialchars((string)$v));
            }
        }
        return $xml->asXML() ?: '';
    }

    public static function fromXML(string $xml): array
    {
        $sx = @simplexml_load_string($xml);
        if ($sx === false) return [];
        $result = [];
        foreach ($sx->item as $item) {
            $row = [];
            foreach ($item->children() as $k => $v) {
                $row[$k] = (string)$v;
            }
            $result[] = $row;
        }
        return $result;
    }
}