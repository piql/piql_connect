<?php

namespace App\Stats;

class Size
{
    const KB = 1024;
    const MB = 1048576;
    const GB = 1073741824;

    public static function convert($size, $num) {
        if($num <= 0) return 0;
        return number_format($num/$size, 2);
    }
}


