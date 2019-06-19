<?php

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'));

Route::get('/wsdl/ac.wsdl', function() {
    $mimeHeader = ['Content-Type: application/wsdl+xsd'];
    $filePath = Storage::path('public/wsdl/ac.wsdl');
    return Response::download($filePath, "ac.wsdl", $mimeHeader);
});

Route::get('/wsdl/archivator_common.xsd', function() {
    $mimeHeader = ['Content-Type: application/wsdl+xsd'];
    $filePath = Storage::path('public/wsdl/archivator_common.xsd');
    return Response::download($filePath, "archivator_common.xsd", $mimeHeader);
});

Route::get('/wsdl/dsca_common.xsd', function() {
    $mimeHeader = ['Content-Type: application/wsdl+xml'];
    $filePath = Storage::path('public/wsdl/dsca_common.xsd');
    return Response::download($filePath, "dsca_common.xsd", $mimeHeader);
});



Route::middleware(['auth'])->group( function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('reports', 'ReportsController@showReports');
    Route::get('settings', 'SettingsController@showSettings');

    Route::prefix('ingest')->group( function () {
        Route::resource('bags', 'IngestBagController')->name('index', 'bags');
        Route::resource('upload', 'IngestUploadController')->name('index', 'upload');
        Route::resource('process', 'IngestProcessController')->name('index', 'process');
        Route::resource('tasks', 'IngestTaskListController')->name('index', 'tasks');
    });

    Route::prefix('access')->group( function () {
        Route::resource('retrieve', 'AccessController')->name('index','retrieve');
    });
});

Auth::routes();

