<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserSettingsController extends Controller
{
    public function updateSettings(Request $request, \App\Interfaces\SettingsInterface $settingsProvider)
    {
        $interfaceLanguage = $request->interface['language'];

        $settings = $settingsProvider->forAuthUser();
        $settings->interfaceLanguage = $interfaceLanguage; //todo: validate form fields
        //App::setLocale( $settings->interfaceLanguage );

        //$settings->defaultAipStorageLocationId = $request->defaultAipStorageLocation; //todo: validate form fields

        //$settings->defaultDipStorageLocationId = $request->defaultDipStorageLocation; //todo: validate form fields

        //if( $request->ingestCompoundMode == "compound" || $request->ingestCompoundMode == "single") {
        //    $settings->ingestCompoundModeEnabled = ( $request->ingestCompoundMode == "compound" );
        //}

        //$settings->ingestMetadataAsFile = ($request->ingestMetadataAsFile == "true"); //todo: validate form fields

        $settings->save();

        return $settings;
    }

    public function updateCurrentUserPassword(Request $request)
    {
        $user = Auth::user();
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;

        // Validate new password
        if (strlen($newPassword) < 8) {
            return response()->json([ 'status' => 1, 'message' => 'The password must be at least 6 characters' ], 200 );
        }

        // Match old password
        if (!Hash::check($oldPassword, $user->password)) {
            return response()->json([ 'status' => 2, 'message' => 'The old password was wrong' ], 200 );
        }

        // Save new password
        $user->password = Hash::make($newPassword);
        $user->save();

        // todo: avoid being logged out

        return response()->json([ 'status' => 0, 'message' => 'Password was updated successfully' ], 200 );
    }
}
