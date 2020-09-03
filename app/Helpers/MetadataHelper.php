<?php

namespace App\Helpers;

class MetadataHelper {
    static function csvEscape($value) {
        $file = fopen('php://memory', 'w+');
        fputcsv($file, array($value));
        rewind($file);
        $value = preg_replace("/[\r\n]+/", '', stream_get_contents($file));
        fclose($file);
        return $value;
    }
}