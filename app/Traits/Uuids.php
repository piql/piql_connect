<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webpatser\Uuid\Uuid;

trait Uuids
{
    protected static function generateBin16Uuid()
    {
        return $this->uuidToBin(Uuid::generate());
    }

    protected static function uuid2Bin($uuid)
    {
        hex2bin(str_replace('-','', (string)$uuid));
    }

    protected static function bin2Uuid($bin16)
    {
        return (string)Uuid::import(bin2hex($bin16));
    }
}
