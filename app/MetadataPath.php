<?php

namespace App;

class MetadataPath
{
    public const ACCOUNT_OBJECT = 'account';
    public const COLLECTION_OBJECT = self::ACCOUNT_OBJECT . '/collection';
    public const HOLDING_OBJECT = self::COLLECTION_OBJECT . '/holding';
    public const FILE_OBJECT_PATH = self::HOLDING_OBJECT . '/';

    public static function of($obj)
    {
        switch ($obj) {
            case 'account':
                return self::ACCOUNT_OBJECT;
            case 'holding':
                return self::HOLDING_OBJECT;
            case 'collection':
                return self::COLLECTION_OBJECT;
            default:
                return self::FILE_OBJECT_PATH;
        }
    }

    public static function count() {
        return 4;
    }
}
