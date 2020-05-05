<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
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
}
