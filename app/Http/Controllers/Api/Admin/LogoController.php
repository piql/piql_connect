<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Log;

class LogoController extends Controller {

    private const LOGO_PATH = 'custom-files/logo.png';

    public function show() {
        return response()->file(storage_path(self::LOGO_PATH));
    }
    public function upload(Request $request) {
		Storage::delete(self::LOGO_PATH);
		Storage::disk('local')->move($request->file('qqfile')->store('tmp'), self::LOGO_PATH);
        return response()->json(['success' => true]);
    }
}
