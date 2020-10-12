<?php

namespace App;

class MetadataPath
{
    public const ACCOUNT_OBJECT = 'account';
    public const ARCHIVE_OBJECT = self::ACCOUNT_OBJECT . '/archive';
    public const HOLDING_OBJECT = self::ARCHIVE_OBJECT . '/holding';
    public const FILE_OBJECT_PATH = self::HOLDING_OBJECT . '/';

    public static function of($obj)
    {
        switch ($obj) {
            case 'account':
                return self::ACCOUNT_OBJECT;
            case 'holding':
                return self::HOLDING_OBJECT;
            case 'archive':
                return self::ARCHIVE_OBJECT;
            default:
                return self::FILE_OBJECT_PATH;
        }
    }

    public static function count() {
        return 4;
    } 
}
