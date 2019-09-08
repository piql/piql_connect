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

Route::group(['prefix' => 'v1' , 'middleware' => 'throttle:500,1','auth:web'], function () {

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

        Route::post('bags', 'Api\Ingest\BagController@store');
        Route::patch('bags/{id}', 'Api\Ingest\BagController@update');
        Route::get('bags/create', 'Api\Ingest\BagController@create');
        Route::get('bags', 'Api\Ingest\BagController@all');
        Route::get('processing', 'Api\Ingest\BagController@processing');
        Route::get('complete', 'Api\Ingest\BagController@complete');
        Route::get('bags/latest', 'Api\Ingest\BagController@latest');
        Route::get('bags/{id}', 'Api\Ingest\BagController@show');
        Route::get('bags/{id}/files', 'Api\Ingest\BagController@showFiles');
        Route::post('bags/{id}/commit', 'Api\Ingest\BagController@commit');
        Route::post('bags/{id}/piql', 'Api\Ingest\BagController@piqlIt');
        Route::get('offline_storage/pending/jobs/{id}/bags', 'Api\Ingest\OfflineStorageController@bags');
        Route::patch('offline_storage/pending/jobs/{id}', 'Api\Ingest\OfflineStorageController@archiveJob');
        Route::get('offline_storage/pending/jobs', 'Api\Ingest\OfflineStorageController@jobs');
        Route::get('offline_storage/archive/jobs', 'Api\Ingest\OfflineStorageController@archiveJobs');


        Route::group(['prefix' => 'am'], function() {
            Route::get('instances', 'Api\Ingest\ArchivematicaServiceController@serviceInstances');
            Route::group(['prefix' => 'transfer'], function () {
                Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@transferStatus');
                Route::post('start/{id}', 'Api\Ingest\ArchivematicaServiceController@startTransfer');
                Route::post('approve/{id}', 'Api\Ingest\ArchivematicaServiceController@approveTransfer');
                Route::delete('{id}' , 'Api\Ingest\ArchivematicaServiceController@transferHideStatus');
            });
            Route::group(['prefix' => 'ingest'], function () {
                Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@ingestStatus');
                Route::delete('{id}' , 'Api\Ingest\ArchivematicaServiceController@ingestHideStatus');
            });
        });
    });

    Route::group(['prefix' => 'planning'], function() {
        Route::apiResource('fonds', 'Api\Planning\FondsController', ['as' => 'planning']);
        Route::apiResource('holdings', 'Api\Planning\HoldingController', ['as' => 'planning']);
    });

    Route::group(['prefix' => 'stats'], function () {
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
