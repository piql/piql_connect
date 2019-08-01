<?php

Route::get('/login', array('uses' => 'Auth\LoginController@showLogin'));
Route::get('/logout', array('uses' => 'Auth\LoginController@logout'));

Route::middleware(['auth', 'locale'])->group( function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('reports', 'ReportsController@showReports');
    Route::get('settings', 'SettingsController@showSettings');
    Route::post('settings', 'SettingsController@updateSettings');

    Route::prefix('ingest')->group( function () {
        Route::resource('bags', 'IngestBagController')->name('index', 'bags');
        Route::resource('upload', 'IngestUploadController')->name('index', 'upload');
        Route::resource('process', 'IngestProcessController')->name('index', 'process');
        Route::resource('tasks', 'IngestTaskListController')->name('index', 'tasks');
        Route::resource('status', 'IngestStatusController')->name('index', 'status');
        Route::get('settings', 'IngestSettingsController@show')->name('show', 'show');
    });

    Route::prefix('access')->group( function () {
        Route::resource('retrieve', 'AccessController')->name('index','retrieve');
    });
});

Auth::routes();

