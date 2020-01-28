<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\UserSetting;
use App;

class SettingsController extends Controller
{

    public function showSettings( \App\Interfaces\SettingsInterface $settingsProvider ){
        return view('/settings/settings', [
            'settings' => $settingsProvider->forAuthUser(),
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations(),
            'singleFileIngestOption' => env('ENABLE_SINGLE_FILE_INGEST_OPTION', false)
        ] );
    }

    public function updateSettings(Request $request, \App\Interfaces\SettingsInterface $settingsProvider )
    {
        $settings = $settingsProvider->forAuthUser();
        $settings->interfaceLanguage = $request->interfaceLanguage; //todo: validate form fields
        App::setLocale( $settings->interfaceLanguage );

        $settings->defaultAipStorageLocationId = $request->defaultAipStorageLocation; //todo: validate form fields

        $settings->defaultDipStorageLocationId = $request->defaultDipStorageLocation; //todo: validate form fields

        if( $request->ingestCompoundMode == "compound" || $request->ingestCompoundMode == "single") {
            $settings->ingestCompoundModeEnabled = ( $request->ingestCompoundMode == "compound" );
        }

        $settings->ingestMetadataAsFile = ($request->ingestMetadataAsFile == "true"); //todo: validate form fields

        $settings->save();

        return view('/settings/settings', [
            'settings' => $settings,
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations(),
            'singleFileIngestOption' => env('ENABLE_SINGLE_FILE_INGEST_OPTION', false)
        ] );
    }

    private function fetchAipStorageLocations()
    {
        return \App\StorageLocation::whereStorableType('App\Aip')->pluck('human_readable_name','id')->toArray();
    }

    private function fetchDipStorageLocations()
    {
        return \App\StorageLocation::whereStorableType('App\Dip')->pluck('human_readable_name','id')->toArray();
    }

}
