<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\UserSetting;
use App;

class SettingsController extends Controller
{

    public function showSettings()
    {
        return view('/settings/settings', [
            'settings' => $this->fetchUserSettings(),
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations()
        ] );
    }

    public function updateSettings(Request $request)
    {
        $userSettings = $this->fetchUserSettings();
        if( $userSettings->interfaceLanguage != $request->interfaceLanguage )
        {
            $userSettings->update([ 'interfaceLanguage' => $request->interfaceLanguage ]); //todo: validate form fields
            App::setLocale( $userSettings->interfaceLanguage );
        }

        if( $userSettings->defaultAipStorageLocationId != $request->defaultAipStorageLocation )
        {
            $userSettings->update([ 'defaultAipStorageLocationId' => $request->defaultAipStorageLocation ]); //todo: validate form fields
        }

        if( $userSettings->defaultDipStorageLocationId != $request->defaultDipStorageLocation )
        {
            $userSettings->update([ 'defaultDipStorageLocationId' => $request->defaultDipStorageLocation ]); //todo: validate form fields
        }


        return view('/settings/settings', [
            'settings' => $this->fetchUserSettings(),
            'aipStorageLocations' => $this->fetchAipStorageLocations(),
            'dipStorageLocations' => $this->fetchDipStorageLocations()
        ] );
    }

    public function fetchUserSettings()
    {
        $userSettings = \Auth::user()->settings;
        if($userSettings == null)
        {
            /* Sane defaults for user settings go here! */
            $userSettings = \Auth::user()->settings()->create([
                'user' => \Auth::user()->id,
                'interface' => array(),
                'workflow' => array(),
                'storage' => array(),
                'data' => array()
            ]);
        }
        return $userSettings;
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
