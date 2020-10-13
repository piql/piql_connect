<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Log;

class ProfileController extends Controller {
    const PROFILE_IMAGE_PATH = 'custom-files/user/';

    public function imgUpload(Request $request) {
        $user = Auth::user();
        $src = storage_path('uploader/profileImg/'.$request->result["name"]);
        $dst = storage_path(self::PROFILE_IMAGE_PATH . $user->id . '.png');
        rename($src, $dst);
        return response()->json(['success' => true]);
    }

    public function img() {
        $user = Auth::user();
        $file = self::PROFILE_IMAGE_PATH . $user->id . '.png';
        if (!file_exists(storage_path($file))) {
            $file = self::PROFILE_IMAGE_PATH . 'avatar.png';
        }
        return response()->file(storage_path($file));
    }
}
