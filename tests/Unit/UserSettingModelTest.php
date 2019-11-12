<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UserSetting;

class UserSettingModelTest extends TestCase
{
    public function test_given_a_user_setting_and_it_has_no_interface_language_when_getting_the_interface_language_it_returns_the_default()
    {
        $userSetting = new UserSetting();
        $userSetting->interface = [];

        $this->assertEquals( env("DEFAULT_INTERFACE_LANGUAGE"), $userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_an_interface_language_when_getting_the_interface_language_it_is_returned()
    {
        $userSetting = new UserSetting();
        $userSetting->interface = ["language" => "someLanguage"];

        $this->assertEquals( "someLanguage", $userSetting->interfaceLanguage );
    }


    public function test_given_a_user_setting_and_it_has_no_interface_language_when_setting_the_interface_language_it_is_updated()
    {
        $userSetting = new UserSetting();
        $userSetting->interface = [];

        $userSetting->interfaceLanguage = "testLanguage";
        $this->assertEquals( "testLanguage", $userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_an_interface_language_when_setting_the_interface_language_it_is_updated()
    {
        $userSetting = new UserSetting();
        $userSetting->interface = ["language" => "originalLanguage"];

        $userSetting->interfaceLanguage = "testLanguage";
        $this->assertEquals( "testLanguage", $userSetting->interfaceLanguage );
    }
    
    public function test_given_a_user_setting_and_it_has_a_default_aip_storage_location_when_getting_the_storage_location_it_is_returned()
    {
        $userSetting = new UserSetting();
        $userSetting->storage = ["defaultAipStorageLocationId" => 20];

        $this->assertEquals( 20, $userSetting->defaultAipStorageLocationId );
    }


    public function test_given_a_user_setting_and_it_has_no_default_aip_storage_location_when_setting_the_storage_location_it_is_updated()
    {
        $userSetting = new UserSetting();
        $userSetting->storage = [];

        $userSetting->defaultAipStorageLocationId = 30;
        $this->assertEquals( 30, $userSetting->defaultAipStorageLocationId );
    }

    public function test_given_a_user_setting_and_it_has_a_default_dip_storage_location_when_getting_the_storage_location_it_is_returned()
    {
        $userSetting = new UserSetting();
        $userSetting->storage = ["defaultDipStorageLocationId" => 40];

        $this->assertEquals( 40, $userSetting->defaultDipStorageLocationId );
    }


    public function test_given_a_user_setting_and_it_has_no_default_dip_storage_location_when_setting_the_storage_location_it_is_updated()
    {
        $userSetting = new UserSetting();
        $userSetting->storage = [];

        $userSetting->defaultDipStorageLocationId = 50;
        $this->assertEquals( 50, $userSetting->defaultDipStorageLocationId );
    }

}
