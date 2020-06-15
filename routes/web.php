<?php

Route::get('/login', array('uses' => 'Auth\LoginController@showLogin'))->name('login');
Route::get('/logout', array('uses' => 'Auth\LoginController@logout'))->name('logout');

Route::middleware(['auth', 'locale', 'activity'])->group( function () {
    Route::get('/{any}', function() {
        return view('index');
    })->where('any', "^(?!api').*$")->name('/'); /* Catch all and pass to the Vue router */

});


Auth::routes();

