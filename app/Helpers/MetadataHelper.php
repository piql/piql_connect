<?php

namespace App\Helpers;

class MetadataHelper {
    static function csvEscape($value, $enclosure='"', $delimiter=',', $escapeChar="\\") {
        $value = str_replace($enclosure, $enclosure . $enclosure, $value);
        if (strchr($value, $delimiter) !== false ||
            strchr($value, $enclosure) !== false ||
            strchr($value, $escapeChar) !== false ||
            strchr($value, "\n") !== false ||
            strchr($value, "\r") !== false ||
            strchr($value, "\t") !== false ||
            strchr($value, ' ') !== false) {
                return $enclosure . $value . $enclosure;
        }
        return $value;
    }
}