<?php

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'));
Route::post('/', function () {
    return view('dashboard');
});

Route::middleware(['auth'])->group( function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('reports', 'ReportsController@showReports');
    Route::get('settings', 'SettingsController@showSettings');

    Route::prefix('ingest')->group( function () {
        Route::resource('upload', 'IngestUploadController')->name('index', 'upload');
    });

    Route::prefix('access')->group( function () {
        Route::resource('retrieve', 'AccessController')->name('index','retrieve');
    });
});

Auth::routes();

