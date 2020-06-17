<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Interfaces\SettingsInterface;

class PreferencesController extends Controller
{

    /* preferences - mapped to GET
     * Get the user preference settings  (User interface setttings, language etc)
     */
    public function preferences( Request $request )
    {
        $user = Auth::user();  //TODO: Pick up the userid from the user_id parameter, validate against Auth::user
        return $user->settings;
    }

    /* updatePreferences - mapped to POST
     * create or update the user preference settings
     */
    public function updatePreferences(Request $request, SettingsInterface $settingsProvider)
    {
        $interfaceLanguage = $request->interface['language'];

        $settings = $settingsProvider->forAuthUser();
        $settings->interfaceLanguage = $interfaceLanguage; //todo: validate form fields

        // TODO: storage locations, compound mode (if we ever want to use that), metadata prefs
        $settings->save();
        return $settings;
    }

}
