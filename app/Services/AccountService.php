<?php

namespace App\Services;

use App\Account;

class AccountService {

	public static function createAccount($uuid, $title, $description, $ownerType, $ownerId) {
        $account = new Account;
        $account->uuid = $uuid;
        $account->title = $title;
        $account->description = $description;
        $account->owner_type = $ownerType;
        $account->owner_id = $ownerId;
        return $account;
    } 
}