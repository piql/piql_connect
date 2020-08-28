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
    Route::post('login', 'Auth\ApiLoginController@login')->middleware('user.checkDisabled');
});

// todo: mode to whitelist middleware or add token to headers in callback
Route::group(['prefix' => 'v1'], function () {
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:api', 'activity']], function () {
    Route::post('logout', 'Auth\ApiLoginController@logout');

    Route::group(['prefix' => 'system'], function () {
        Route::get('statuses/current-user', 'Api\System\StatusController@currentUser');

        Route::get('users/{user_id}/preferences', 'Api\Users\PreferencesController@preferences');
        Route::post('users/{user_id}/preferences', 'Api\Users\PreferencesController@updatePreferences');

        Route::post('users/{user_id}/password', 'Api\Users\SelfServiceController@updatePassword');

        Route::get('languages', 'Api\System\SystemController@languages');
        Route::get('system/session-lifetime', 'Api\System\SystemController@sessionLifetime');
        Route::get('users/me', 'Api\Users\SelfServiceController@me');
    });

    Route::group(['prefix' => 'ingest'], function () {
        // todo: getNewBag
        // todo: getOpenBags

        Route::post('bags', 'Api\Ingest\BagController@store')->name('api.ingest.bags.store');
        Route::patch('bags/{id}', 'Api\Ingest\BagController@update')->name('api.ingest.bags.update');
        Route::get('bags/create', 'Api\Ingest\BagController@create')->name('api.ingest.bags.create');
        Route::get('bags', 'Api\Ingest\BagController@all')->name('api.ingest.bags.all');
        Route::get('processing', 'Api\Ingest\BagController@processing')->name('api.ingest.bags.processing');
        Route::get('bags/complete', 'Api\Ingest\BagController@complete')->name('api.ingest.bags.complete');
        Route::get('bags/offline', 'Api\Ingest\BagController@offline')->name('api.ingest.bags.offline');
        Route::get('bags/online', 'Api\Ingest\BagController@online')->name('api.ingest.bags.online');
        Route::get('bags/latest', 'Api\Ingest\BagController@latest')->name('api.ingest.bags.latest'); //FIXME per user
        Route::get('bags/{id}', 'Api\Ingest\BagController@show')->name('api.ingest.bags.show');

        Route::get('bags/{id}/files', 'Api\Ingest\BagController@showFiles');
        Route::post('bags/{bagId}/files', 'Api\Ingest\FileUploadController@store'); //Create a file model in the db, push an event
        Route::delete('bags/{bagId}/files/{fileId}', 'Api\Ingest\BagController@deleteFile');
        Route::get('bags/files/{file}', 'Api\Ingest\BagController@showFile');

        Route::post('bags/{id}/commit', 'Api\Ingest\BagController@commit')->name('api.ingest.bags.commit');
        Route::post('bags/{id}/piql', 'Api\Ingest\BagController@piqlIt');
        Route::get('bags/{id}/download', 'Api\Ingest\BagController@download');

        Route::post('files/upload', '\Optimus\FineuploaderServer\Controller\LaravelController@upload');
        Route::delete('files/{upload_path}', 'Api\Ingest\FileUploadController@deleteUploadedTemp');
        Route::post('files/filename/validate', 'Api\Ingest\FileUploadController@validateFileName');
        Route::get('files/uploaded', 'Api\Ingest\FileUploadController@all');
        Route::get('files/uploaded/{id}', 'Api\Ingest\FileUploadController@show');
        Route::post('files/bag', 'Api\Ingest\BagController@bagSingleFile');
        Route::get('files/{id}/download', 'Api\Ingest\BagController@downloadFile');
        Route::get('files/{file}/metadata', 'Api\Ingest\FileMetadataController@index')->name('api.ingest.files.metadata.index');
        Route::get('files/{file}/metadata/{metadata}', 'Api\Ingest\FileMetadataController@show')->name('api.ingest.files.metadata.show');
        Route::post('files/{file}/metadata', 'Api\Ingest\FileMetadataController@store')->name('api.ingest.files.metadata.store');
        Route::patch('files/{file}/metadata/{metadata}', 'Api\Ingest\FileMetadataController@update')->name('api.ingest.files.metadata.update');
        Route::delete('files/{file}/metadata/{metadata}', 'Api\Ingest\FileMetadataController@destroy')->name('api.ingest.files.metadata.destroy');

        Route::get('storage/offline/pending/buckets/{bucket}/metadata', 'Api\Ingest\BucketMetadataController@index')->name('api.ingest.bucket.metadata.index');
        Route::post('storage/offline/pending/buckets/{bucket}/metadata', 'Api\Ingest\BucketMetadataController@store')->name('api.ingest.bucket.metadata.store');
        Route::patch('storage/offline/pending/buckets/{bucket}/metadata/{metadata}', 'Api\Ingest\BucketMetadataController@update')->name('api.ingest.bucket.metadata.update');


        Route::get('storage/offline/pending/buckets/{id}', 'Api\Ingest\OfflineStorageController@job')->name('api.ingest.bucket');
        Route::get('storage/offline/pending/buckets/{id}/dips', 'Api\Ingest\OfflineStorageController@dips')->name('api.ingest.bucket.dips');
        Route::patch('storage/offline/pending/buckets/{id}', 'Api\Ingest\OfflineStorageController@update')->name('api.ingest.bucket.update');
        Route::delete('storage/offline/pending/buckets/{id}', 'Api\Ingest\OfflineStorageController@delete')->name('api.ingest.bucket.delete');
        Route::delete('storage/offline/pending/buckets/{id}/dips/{dipId}', 'Api\Ingest\OfflineStorageController@detachDip')->name('api.ingest.bucket.detach.dip');
        Route::get('storage/offline/pending/buckets', 'Api\Ingest\OfflineStorageController@jobs')->name('api.ingest.buckets.pending');
        Route::get('storage/offline/archive/buckets', 'Api\Ingest\OfflineStorageController@archiveJobs')->name('api.ingest.buckets.archiving');

        Route::post('storage/offline/{jobId}/config/upload', 'Api\Storage\BucketConfigController@upload')->name('api.ingest.buckets.config');
        Route::get('storage/offline/{jobId}/config/showFiles', 'Api\Storage\BucketConfigController@showFiles')->name('api.ingest.buckets.config');
        Route::get('storage/offline/{jobId}/config/showFile/{name}', 'Api\Storage\BucketConfigController@showFile')->name('api.ingest.buckets.config');
        Route::post('storage/offline/{jobId}/config/removeFile/{name}', 'Api\Storage\BucketConfigController@removeFile')->name('api.ingest.buckets.config');

        Route::group(['prefix' => 'triggers'], function () {
            // todo: add middleware
            Route::group(['prefix' => 'am'], function () {
                Route::post('{serviceUuid}/uploaded/{packageUuid}', 'Api\Ingest\ArchivematicaServiceCallback@packageUploaded')->name('api.ingest.triggers.am.callback');
            });
        });

        // todo: these calls are obsolete
        Route::group(['prefix' => 'am'], function () {
            Route::get('instances', 'Api\Ingest\ArchivematicaServiceController@serviceInstances')->name('api.am.instances.index');
            Route::group(['prefix' => 'transfer'], function () {
                Route::get('status', 'Api\Ingest\ArchivematicaServiceController@transferStatus')->name('api.am.transfer.status');
                Route::post('start/{id}', 'Api\Ingest\ArchivematicaServiceController@startTransfer')->name('api.am.transfer.start');
                Route::post('approve/{id}', 'Api\Ingest\ArchivematicaServiceController@approveTransfer')->name('api.am.transfer.approve');
                Route::delete('{id}', 'Api\Ingest\ArchivematicaServiceController@transferHideStatus')->name('api.am.transfer.hide');
            });
            Route::group(['prefix' => 'ingest'], function () {
                Route::get('status', 'Api\Ingest\ArchivematicaServiceController@ingestStatus');
                Route::delete('{id}', 'Api\Ingest\ArchivematicaServiceController@ingestHideStatus');
            });
        });
    });

    Route::group(['prefix' => 'access'], function () {
        Route::get('aips/{aipId}', 'Api\Access\AipController@show');
        Route::get('aips/{aipId}/filename', 'Api\Access\AipController@filename');
        Route::get('aips/dips/{dipId}/filename', 'Api\Access\AipController@filenameFromDipId');
        Route::get('aips/{aipId}/download', 'Api\Access\AipController@download');
        Route::get('aips/dips/{dipId}/download', 'Api\Access\AipController@downloadFromDipId');
        Route::get('dips', 'Api\Access\DipController@index');
        Route::get('dips/{dipId}', 'Api\Access\DipController@show');
        Route::get('dips/{dipId}/files', 'Api\Access\DipController@files');
        Route::get('dips/{dipId}/files/{fileId}', 'Api\Access\DipController@showFile');
        Route::get('dips/{dipId}/thumbnails', 'Api\Access\DipController@package_thumbnail');
        Route::get('dips/{dipId}/thumbnails/files/{fileId}', 'Api\Access\DipController@file_thumbnail');
        Route::get('dips/{dipId}/previews', 'Api\Access\DipController@package_preview');
        Route::get('dips/{dipId}/previews/files/{fileId}', 'Api\Access\DipController@file_preview');
        Route::get('dips/{dipId}/aipfile/{fileId}', 'Api\Access\DipController@aipFile');
        Route::get('dips/{dipId}/downloads/files/{fileId}', 'Api\Access\DipController@file_download');
        Route::get('aips/{dipId}/downloads/files/{fileId}', 'Api\Access\AipController@download');
        Route::get('aips/{aipId}/file/{fileId}/download', 'Api\Access\AipController@fileDownload');

        Route::get('files/{file}/metadata', 'Api\Access\FileObjectMetadataController@index')->name('api.access.files.metadata.index');
    });


    Route::group(['prefix' => 'planning'], function () {
        Route::apiResource('holdings', 'Api\Planning\HoldingController', ['as' => 'planning']);
        Route::apiResource('archives', 'Api\Planning\ArchiveController', ['as' => 'planning']);
        Route::apiResource('archives.holdings', 'Api\Planning\ArchiveHoldingController', ['as' => 'planning']);
    });

    Route::group(['prefix' => 'storage'], function () {
        Route::get('transfer/locations/{id}', 'Api\Storage\TransferController@index')->name('storage.transfer.ls');
        Route::post('transfer/locations/{id}', 'Api\Storage\TransferController@store')->name('storage.transfer.upload');
        Route::post('transfer/locations/{id}/copy', 'Api\Storage\TransferController@copy')->name('storage.transfer.download');
        Route::post('transfer/locations/{id}/deletion', 'Api\Storage\TransferController@deletion')->name('storage.transfer.deletion');
        Route::get('config/locations', 'Api\Storage\StorageLocationController@index')->name('storage.config.locations');
        Route::get('config/locations/{id}', 'Api\Storage\StorageLocationController@show')->name('storage.config.location');
        Route::post('config/locations', 'Api\Storage\StorageLocationController@store')->name('storage.config.location.new');
        Route::get('retrievals/latest', 'Api\Storage\RetrievalCollectionController@latest');
        Route::get('retrievals/retrieving', 'Api\Storage\RetrievalCollectionController@retrieving');
        Route::get('retrievals/toready', 'Api\Storage\RetrievalCollectionController@toReady');
        Route::get('retrievals/ready', 'Api\Storage\RetrievalCollectionController@ready');
        Route::post('retrievals/{id}/take-offline', 'Api\Storage\RetrievalCollectionController@takeOffline');
        Route::post('retrievals/add', 'Api\Storage\RetrievalCollectionController@addToLatest');
        Route::get('retrievals/{id}/files', 'Api\Storage\RetrievalCollectionController@files');
        Route::post('retrievals/{id}/close', 'Api\Storage\RetrievalCollectionController@close');
        Route::get('retrievals/{id}/download', 'Api\Storage\RetrievalCollectionController@download');
        Route::get('requests/aips', 'Api\Storage\RetrievalJobsController@aipsOnFilm')->name('storage.buckets.aips');
        Route::get('buckets/{id}', 'Api\Storage\RetrievalJobsController@show')->name('storage.buckets');
    });


    Route::group(['prefix' => 'stats', 'middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'charts'], function () {
            Route::get('aips/online/ingested', 'Api\Stats\ChartController@onlineAIPsIngested');
            Route::get('aips/online/ingested/monthly', 'Api\Stats\ChartController@monthlyOnlineAIPsIngested')->name('monthlyOnlineAIPsIngested');
            Route::get('data/online/ingested/monthly', 'Api\Stats\ChartController@monthlyOnlineDataIngested')->name('monthlyOnlineDataIngested');
            Route::get('aips/online/accessed/monthly', 'Api\Stats\ChartController@monthlyOnlineAIPsAccessed')->name('monthlyOnlineAIPsAccessed');
            Route::get('data/online/accessed/monthly', 'Api\Stats\ChartController@monthlyOnlineDataAccessed')->name('monthlyOnlineDataAccessed');
            Route::get('aips/online/ingested/daily', 'Api\Stats\ChartController@dailyOnlineAIPsIngested')->name('dailyOnlineAIPsIngested');
            Route::get('data/online/ingested/daily', 'Api\Stats\ChartController@dailyOnlineDataIngested')->name('dailyOnlineDataIngested');
            Route::get('aips/online/accessed/daily', 'Api\Stats\ChartController@dailyOnlineAIPsAccessed')->name('dailyOnlineAIPsAccessed');
            Route::get('data/online/accessed/daily', 'Api\Stats\ChartController@dailyOnlineDataAccessed')->name('dailyOnlineDataAccessed');
            Route::get('file-formats/ingested', 'Api\Stats\ChartController@onlineFileFormatsIngested')->name('fileFormatsIngested');
        });
        Route::get('user/{userId}', 'Api\Stats\UserStatsController@userStats')->name('userstats');
    });
});

Route::group(['prefix' => 'v1/admin/access-control'], function () {
    Route::get('', 'Api\Admin\AccessControlController@index');
    Route::get('permissions', 'Api\Admin\AccessControlController@listPermissions');
    Route::post('permissions', 'Api\Admin\AccessControlController@createPermission');
    Route::get('permissions/{id}', 'Api\Admin\AccessControlController@show')->where('id', '[0-9]+');
    Route::put('permissions/{id}', 'Api\Admin\AccessControlController@update')->where('id', '[0-9]+');
    Route::put('permissions/{id}/set-group/{groupId}', 'Api\Admin\AccessControlController@updateGroup')->where(['id' => '[0-9]+', 'groupId' => '[0-9]+']);
    Route::delete('permissions/{id}', 'Api\Admin\AccessControlController@destroy')->where('id', '[0-9]+');
    Route::get('permissions/{id}/users', 'Api\Admin\AccessControlController@users');

    Route::get('roles', 'Api\Admin\AccessControlController@listRoles');
    Route::post('roles', 'Api\Admin\AccessControlController@createRole');
    Route::get('roles/{id}', 'Api\Admin\AccessControlController@show')->where('id', '[0-9]+');
    Route::put('roles/{id}', 'Api\Admin\AccessControlController@update')->where('id', '[0-9]+');
    Route::delete('roles/{id}', 'Api\Admin\AccessControlController@destroy')->where('id', '[0-9]+');
    Route::get('roles/{id}/users', 'Api\Admin\AccessControlController@users');
    Route::get('roles/{id}/permissions', 'Api\Admin\AccessControlController@listRolePermissions')->where('id', '[0-9]+');
    Route::post('roles/{id}/permissions', 'Api\Admin\AccessControlController@addPermissionsToRole')->where('id', '[0-9]+');
    // Route::post('roles/{id}/permissions', 'Api\Admin\AccessControlController@handleRolePermissionAssignment')->where('id', '[0-9]+');

    Route::get('permission-groups', 'Api\Admin\AccessControlController@listPermissionGroups');
    Route::post('permission-groups', 'Api\Admin\AccessControlController@createPermissionGroup');
    Route::get('permission-groups/{id}', 'Api\Admin\AccessControlController@getPermissionGroup')->where('id', '[0-9]+');
    Route::put('permission-groups/{id}', 'Api\Admin\AccessControlController@update')->where('id', '[0-9]+');
    Route::delete('permission-groups/{id}', 'Api\Admin\AccessControlController@destroy')->where('id', '[0-9]+');
    Route::get('permission-groups/{id}/permissions', 'Api\Admin\AccessControlController@listPermissionGroupPermissions')->where('id', '[0-9]+');
    Route::post('permission-groups/{id}/permission', 'Api\Admin\AccessControlController@createPermissionGroupPermission')->where('id', '[0-9]+');

    Route::post('users/assign', 'Api\Admin\AccessControlController@assignUsers');
    Route::post('users/unassign', 'Api\Admin\AccessControlController@unAssignUsers');
    Route::post('users/has-access-control', 'Api\Admin\AccessControlController@userHasAccessControl');
    Route::get('users/{id}/permissions', 'Api\Admin\AccessControlController@userPermissions');
    Route::get('users/{id}/access', 'Api\Admin\AccessControlController@userAccess');
});

Route::group(['prefix' => 'v1/admin/users'], function () {
    Route::get('', 'Api\Admin\UserController@index');
    Route::get('{id}', 'Api\Admin\UserController@show');
    Route::post('disable', 'Api\Admin\UserController@disable');
    Route::post('enable', 'Api\Admin\UserController@enable');
});


Route::group(['prefix' => 'v1/admin/logo'], function () {
    Route::get('show', 'Api\Admin\LogoController@show');
    Route::post('upload', 'Api\Admin\LogoController@upload');
});

Route::group(['prefix' => 'v1/registration'], function () {
    Route::post('register', 'Api\Registration\UserRegistrationController@register');
    Route::post('confirm', 'Api\Registration\UserRegistrationController@confirm');
});

Route::group(['prefix' => 'v1/media'], function () {
    Route::get('thumb/{fileName}', 'Api\System\MediaController@thumb');
    Route::get('dips/{dipId}/previews/files/{fileId}', 'Api\System\MediaController@showDipFile');
});
        