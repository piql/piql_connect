<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\UserSetting;
use App;

class SettingsController extends Controller
{

    public function showSettings( \App\Interfaces\SettingsInterface $settingsService )
    {
        $settings = $settingsService->all();
        return view('/settings/settings', [
            'settings' => $settings,
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations()
        ] );
    }

    public function updateSettings(Request $request, \App\Interfaces\SettingsInterface $settingsService )
    {
        $settings = $settingsService->all();
        $settings->interfaceLanguage = $request->interfaceLanguage; //todo: validate form fields
        App::setLocale( $settings->interfaceLanguage );

        $settings->defaultAipStorageLocationId = $request->defaultAipStorageLocation; //todo: validate form fields

        $settings->defaultDipStorageLocationId = $request->defaultDipStorageLocation; //todo: validate form fields

        $settings->ingestCompoundModeEnabled = ( $request->ingestCompoundMode == "compound" );

        $settings->save();


        return view('/settings/settings', [
            'settings' => $settings,
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations()
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
