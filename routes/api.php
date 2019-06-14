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

Route::group(['prefix' => 'v1' /*, 'middleware' => 'auth:api'*/], function () {
    Route::group(['prefix' => 'ingest'], function() {
        Route::post('upload', '\Optimus\FineuploaderServer\Controller\LaravelController@upload');
        //Route::apiResource('upload', 'FileUploadController');
    });
});

//Route::group(['prefix' => 'v1' /*, 'middleware' => 'auth:api' */], function() {
//Route::post('/api/files/upload', 'Api\Files\UploadController@uploadFile');
//Route::get('/api/files/upload', 'Api\Files\UploadController@getFile');
//});
