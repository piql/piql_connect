<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'Auth\ApiLoginController@login');
});

Route::group(['prefix' => 'v1' , 'middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\ApiLoginController@logout');

    Route::group(['prefix' => 'system'], function () {
        Route::get('currentUser', 'Api\System\StatusController@currentUser');
        Route::get('currentBag', 'Api\System\StatusController@currentBag');
    });

    Route::group(['prefix' => 'ingest'], function() {
        Route::post('upload', '\Optimus\FineuploaderServer\Controller\LaravelController@upload');
        Route::post('fileUploaded', 'Api\Ingest\FileUploadController@store');
        Route::get('uploaded/', 'Api\Ingest\FileUploadController@all');
        Route::get('uploaded/{id}', 'Api\Ingest\FileUploadController@show');

        // todo: getNewBag
        // todo: getOpenBags

        Route::post('bags', 'Api\Ingest\BagController@store')->name('api.ingest.bags.store');
        Route::patch('bags/{id}', 'Api\Ingest\BagController@update')->name('api.ingest.bags.update');
        Route::get('bags/create', 'Api\Ingest\BagController@create')->name('api.ingest.bags.create');
        Route::get('bags', 'Api\Ingest\BagController@all')->name('api.ingest.bags.all');
        Route::get('processing', 'Api\Ingest\BagController@processing')->name('api.ingest.bags.processing');
        Route::get('complete', 'Api\Ingest\BagController@complete')->name('api.ingest.bags.complete');
        Route::get('bags/offline', 'Api\Ingest\BagController@offline')->name('api.ingest.bags.offline');
        Route::get('bags/online', 'Api\Ingest\BagController@online')->name('api.ingest.bags.online');
        Route::get('bags/latest', 'Api\Ingest\BagController@latest')->name('api.ingest.bags.latest');
        Route::get('bags/{id}', 'Api\Ingest\BagController@show')->name('api.ingest.bags.show');
        Route::get('bags/{id}/files', 'Api\Ingest\BagController@showFiles');
        Route::post('bags/{id}/commit', 'Api\Ingest\BagController@commit');
        Route::post('bags/{id}/piql', 'Api\Ingest\BagController@piqlIt');
        Route::get('bags/{id}/download', 'Api\Ingest\BagController@download');
        Route::get('files/{id}/download', 'Api\Ingest\BagController@downloadFile');
        Route::get('offline_storage/pending/jobs/{id}/bags', 'Api\Ingest\OfflineStorageController@bags');
        Route::patch('offline_storage/pending/jobs/{id}', 'Api\Ingest\OfflineStorageController@archiveJob');
        Route::get('offline_storage/pending/jobs', 'Api\Ingest\OfflineStorageController@jobs');
        Route::get('offline_storage/archive/jobs', 'Api\Ingest\OfflineStorageController@archiveJobs');


        Route::group(['prefix' => 'am'], function() {
            Route::get('instances', 'Api\Ingest\ArchivematicaServiceController@serviceInstances')->name('api.am.instances.index');
            Route::group(['prefix' => 'transfer'], function () {
                Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@transferStatus')->name('api.am.transfer.status');
                Route::post('start/{id}', 'Api\Ingest\ArchivematicaServiceController@startTransfer')->name('api.am.transfer.start');
                Route::post('approve/{id}', 'Api\Ingest\ArchivematicaServiceController@approveTransfer')->name('api.am.transfer.approve');
                Route::delete('{id}' , 'Api\Ingest\ArchivematicaServiceController@transferHideStatus')->name('api.am.transfer.hide');
            });
            Route::group(['prefix' => 'ingest'], function () {
                Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@ingestStatus');
                Route::delete('{id}' , 'Api\Ingest\ArchivematicaServiceController@ingestHideStatus');
            });
        });
    });

    Route::group(['prefix' => 'planning'], function() {
        Route::apiResource('fonds', 'Api\Planning\FondsController', ['as' => 'planning']);
        Route::apiResource('archives', 'Api\Planning\ArchiveController', ['as' => 'planning']);
        Route::apiResource('archives.fonds', 'Api\Planning\ArchiveFondsController', ['as' => 'planning']);
    });

    Route::group(['prefix' => 'storage'], function() {
        Route::get('retrievals/latest', 'Api\Storage\RetrievalCollectionController@latest');
        Route::get('retrievals/retrieving', 'Api\Storage\RetrievalCollectionController@retrieving');
        Route::get('retrievals/toready', 'Api\Storage\RetrievalCollectionController@toReady');
        Route::get('retrievals/ready', 'Api\Storage\RetrievalCollectionController@ready');
        Route::post('retrievals/{id}/take_offline', 'Api\Storage\RetrievalCollectionController@takeOffline');
        Route::post('retrievals/add', 'Api\Storage\RetrievalCollectionController@addToLatest');
        Route::get('retrievals/{id}/files', 'Api\Storage\RetrievalCollectionController@files');
        Route::post('retrievals/{id}/close', 'Api\Storage\RetrievalCollectionController@close');
        Route::get('retrievals/{id}/download', 'Api\Storage\RetrievalCollectionController@download');
        Route::apiResource('retrievals', 'Api\Storage\RetrievalCollectionController', ['as' => 'retrievals']);
    });


    Route::group(['prefix' => 'stats', 'middleware' => 'auth:api'], function () {
        Route::get('monthlyOnlineAIPsIngested', 'Api\Stats\DashboardChartController@monthlyOnlineAIPsIngestedEndpoint')->name('monthlyOnlineAIPsIngested');
        Route::get('monthlyOnlineDataIngested', 'Api\Stats\DashboardChartController@monthlyOnlineDataIngestedEndpoint')->name('monthlyOnlineDataIngested');
        Route::get('monthlyOnlineAIPsAccessed', 'Api\Stats\DashboardChartController@monthlyOnlineAIPsAccessedEndpoint')->name('monthlyOnlineAIPsAccessed');
        Route::get('monthlyOnlineDataAccessed', 'Api\Stats\DashboardChartController@monthlyOnlineDataAccessedEndpoint')->name('monthlyOnlineDataAccessed');
        Route::get('dailyOnlineAIPsIngested', 'Api\Stats\DashboardChartController@dailyOnlineAIPsIngestedEndpoint')->name('dailyOnlineAIPsIngested');
        Route::get('dailyOnlineDataIngested', 'Api\Stats\DashboardChartController@dailyOnlineDataIngestedEndpoint')->name('dailyOnlineDataIngested');
        Route::get('dailyOnlineAIPsAccessed', 'Api\Stats\DashboardChartController@dailyOnlineAIPsAccessedEndpoint')->name('dailyOnlineAIPsAccessed');
        Route::get('dailyOnlineDataAccessed', 'Api\Stats\DashboardChartController@dailyOnlineDataAccessedEndpoint')->name('dailyOnlineDataAccessed');
        Route::get('fileFormatsIngested', 'Api\Stats\DashboardChartController@fileFormatsIngestedEndpoint')->name('fileFormatsIngested');
    });
});
