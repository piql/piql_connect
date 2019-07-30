<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\UserSetting;

class SettingsController extends Controller
{
    public function __construct()
    {

    }

    public function showSettings()
    {
        return view('/settings/settings', $this->fetchUserSettings());
    }

    public function updateSettings(Request $request)
    {
        $userSettings = $this->fetchUserSettings();
        $userSettings->interfaceLanguage = $request->interfaceLanguage; //todo: verify fields
        $userSettings->update();
        return view('/settings/settings', $userSettings);
    }

    public function fetchUserSettings()
    {
        $userSettings = \Auth::user()->settings()->first();
        if($userSettings == null)
        {
            /* Sane defaults for user settings go here! */
            $userSettings = \Auth::user()->settings()->create([
                'user' => \Auth::user()->id,
                'interfaceLanguage' => 'en'
            ]);
        }
        return $userSettings;
    }

}
