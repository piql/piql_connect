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
        Route::post('bags', 'Api\Ingest\BagController@store');
        Route::patch('bags/{id}', 'Api\Ingest\BagController@update');
        Route::get('bags', 'Api\Ingest\BagController@all');
        Route::get('processing', 'Api\Ingest\BagController@processing');
        Route::get('complete', 'Api\Ingest\BagController@complete');
        Route::get('bags/{id}', 'Api\Ingest\BagController@show');
        Route::get('bags/{id}/files', 'Api\Ingest\BagController@showFiles');
        Route::post('bags/{id}/commit', 'Api\Ingest\BagController@commit');
        Route::post('bags/{id}/piql', 'Api\Ingest\BagController@piqlIt');

        Route::group(['prefix' => 'am'], function() {
            Route::get('instances', 'Api\Ingest\ArchivematicaServiceController@serviceInstances');
                Route::group(['prefix' => 'transfer'], function () {
                    Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@transferStatus');
                    Route::post('start/{id}', 'Api\Ingest\ArchivematicaServiceController@startTransfer');
                    Route::post('approve/{id}', 'Api\Ingest\ArchivematicaServiceController@approveTransfer');
                });
                Route::group(['prefix' => 'ingest'], function () {
                    Route::get('status' , 'Api\Ingest\ArchivematicaServiceController@transferStatus');
                });
        });
    });

    Route::group(['prefix' => 'stats'], function () {
        Route::get('monthlyOnlineAIPsIngested', 'Api\Stats\DashboardChartController@monthlyOnlineAIPsIngestedEndpoint')->name('monthlyOnlineAIPsIngested');
        Route::get('monthlyOnlineDataIngested', 'Api\Stats\DashboardChartController@monthlyOnlineDataIngestedEndpoint')->name('monthlyOnlineDataIngested');
        Route::get('fileFormatsIngested', 'Api\Stats\DashboardChartController@fileFormatsIngestedEndpoint')->name('fileFormatsIngested');
    });
});
