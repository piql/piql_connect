<?php

Route::get('/login', array('uses' => 'Auth\LoginController@showLogin'))->name('login');
Route::get('/logout', array('uses' => 'Auth\LoginController@logout'))->name('logout');

Route::middleware(['auth', 'locale', 'activity'])->group( function () {

    Route::get('/', 'DashboardController@showDashboard')->name('dashboard');
    Route::get('reports', 'ReportsController@showReports');
    Route::get('settings', 'SettingsController@showSettings')->name('web.showSettings');
    Route::post('settings', 'SettingsController@updateSettings')->name('web.updateSettings');

    Route::prefix('ingest')->group( function () {
        Route::resource('bags', 'IngestBagController')->name('index', 'bags');
        Route::resource('upload', 'IngestUploadController')->name('index', 'upload');
        Route::resource('process', 'IngestProcessController')->name('index', 'process');
        Route::resource('tasks', 'IngestTaskListController')->name('index', 'tasks');
        Route::get('tasks/{bagId}', 'IngestTaskListController@show')->name('bag.show.files', 'bag.show.files');
        Route::get('tasks/{bagId}/metadata/{fileId}/edit', 'IngestMetadataController@edit')->name('metadata.edit', 'bag');
        Route::get('tasks/{bagId}/metadata/{fileId}/edit_ingest', 'IngestMetadataController@editFromUpload')->name('metadata.edit.upload', 'bag');
        Route::resource('status', 'IngestStatusController')->name('index', 'status');
        Route::get('settings', 'IngestSettingsController@show')->name('show', 'show');
        Route::get('offline_storage', 'IngestOfflineStorageController@index')->name('offline_storage', 'index');
        Route::get('offline_storage/{id}', 'IngestOfflineStorageController@show')->name('offline_storage.show', 'show');
        Route::get('offline_storage/{id}/metadata', 'IngestOfflineStorageController@metadataEdit')->name('offline_storage.metadata.edit');
        Route::get('offline_storage/{job}/configuration', 'IngestOfflineStorageController@configurationEdit')->name('offline_storage.setup');
    });

    Route::prefix('access')->group( function () {
        Route::get('browse/files/{fileId}/metadata', 'BrowseController@accessShowMetadata')->name('access.browse.file.metadata');
        Route::get('browse', 'BrowseController@index')->name('access.browse');
        Route::redirect('retrieve', 'retrieve/ready');
        Route::get('retrieve/ready', 'AccessController@ready')->name('access.retrieve.ready');
        Route::get('retrieve/retrieving', 'AccessController@retrieving')->name('access.retrieve.retrieving');
        Route::get('retrieve/download', 'AccessController@download')->name('access.retrieve.downloadable');
        Route::get('retrieve/history', 'AccessController@history')->name('access.retrieve.history');
    });

    Route::prefix('planning')->group( function () {
        Route::get('archives', function () {
            return view('settings/planning/archives/edit');
        })->name('planning.archives');
        Route::get('holdings', function () {
            return view('settings/planning/holdings/configure');
        })->name('planning.holdings');

    });

});

Auth::routes();

