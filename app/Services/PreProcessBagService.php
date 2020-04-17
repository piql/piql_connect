<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\FilesystemAdapter;
use Illuminate\Support\Str;
use Log;

class PreProcessBagService implements \App\Interfaces\PreProcessBagInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function process( \App\Bag $bag) : array {
        return [$bag];
    }

}
