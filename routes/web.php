<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function() {
    return view('index');
})->where('any', "^(?!api').*$")->name('/'); /* Catch all and pass to the Vue router */

