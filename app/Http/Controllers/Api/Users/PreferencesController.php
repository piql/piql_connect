<?php

namespace App\Http\Controllers\Api\Users;

use App\Traits\UserSettingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Interfaces\SettingsInterface;
use Illuminate\Support\Facades\Validator;

class PreferencesController extends Controller
{

    use UserSettingRequest;

    /* preferences - mapped to GET
     * Get the user preference settings  (User interface setttings, language etc)
     */
    public function preferences(Request $request)
    {
        $user = Auth::user();  //TODO: Pick up the userid from the user_id parameter, validate against Auth::user
        if (!isset($user->settings->interface['tableRowCount'])) {
            $user->settings->interface = $user->settings->interface + ['tableRowCount' => $this->rowLimit($user, $request) ];
        }

        return $user->settings;
    }

    /* updatePreferences - mapped to POST
     * create or update the user preference settings
     */
    public function updatePreferences(Request $request, SettingsInterface $settingsProvider)
    {
        $validator = Validator::make($request->all(), ['interface' => 'required']);
        if ($validator->fails())
            return response(['message' => '"interface" is required', 'errors' => $validator->errors(),], 400);

        $params = collect($request->interface)->only(['language', 'tableRowCount'])->all();
        if (empty($params))
            return response(['message' => 'no valid settings found in request'], 400);

        $settings = $settingsProvider->forAuthUser();
        if (isset($params['language'])) $settings->interfaceLanguage = $params['language']; //todo: validate form fields
        if (isset($params['tableRowCount'])) $settings->interfaceTableRowCount = $params['tableRowCount']; //todo: validate form fields

        // TODO: storage locations, compound mode (if we ever want to use that), metadata prefs
        $settings->save();
        return $settings;
    }
}
