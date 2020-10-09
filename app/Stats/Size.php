<?php

namespace App\Stats;

class Size
{
    const KB = 1000;
    const MB = 1000000;
    const GB = 1000000000;

    public static function convert($size, $num) {
        if($num <= 0) return 0;
        return (float) number_format($num/$size, 2);
    }
}


