<?php

namespace App\Interfaces;

interface PreProcessBagInterface
{
    public function process( \App\Bag $bag) : array; /*  */
}
