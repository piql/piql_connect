<?php

namespace App\Traits;

use App\Auth\User;
use Illuminate\Http\Request;

trait UserSettingRequest
{
    public static function rowLimit(User $user , Request $request)
    {
        if($request->limit) return $request->limit;
        $uiConfig = $user->settings->interface;
        if(isset($uiConfig->tableRowCount)) return $uiConfig->tableRowCount;
        return env('DEFAULT_ENTRIES_PER_PAGE', 10);
    }
}