<?php

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'));
Route::post('/', function () {
    return view('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('upload', 'IngestController@showUpload');
    Route::get('retrieve', 'AccessController@showRetrieve');
    Route::get('reports', 'ReportsController@showReports');
    Route::get('settings', 'SettingsController@showSettings');
});

Auth::routes();

