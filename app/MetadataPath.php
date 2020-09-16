<?php

namespace App;

class MetadataPath
{
    public const ACCOUNT_OBJECT = 'account';
    public const ARCHIVE_OBJECT = self::ACCOUNT_OBJECT.'/archive';
    public const HOLDING_OBJECT = self::ARCHIVE_OBJECT.'/holding';
    public const FILE_OBJECT_PATH = self::HOLDING_OBJECT.'/';
}
