<?php

namespace App\Services;

use \App\Interfaces\IngestValidationInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\FilesystemAdapter;
use Illuminate\Support\Str;
use Log;

class IngestValidationService implements IngestValidationInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function validateFileName( $string) : bool {
        return true;
    }

}
