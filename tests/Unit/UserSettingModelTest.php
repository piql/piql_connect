<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\UserSetting;

class UserSettingModelTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        $this->userSetting = new UserSetting();
    }

    public function test_given_a_config_group_that_does_not_exist_when_getting_a_property_from_it_then_it_throws()
    {
        $this->expectExceptionMessage("The configuration group 'nosuchgroup' does not exist." );

        $this->userSetting->get("nosuchgroup.test");
    }

    public function test_given_a_config_group_that_does_not_exist_when_setting_a_property_to_it_then_it_throws()
    {
        $this->expectExceptionMessage("The configuration group 'nosuchgroup' does not exist." );

        $this->userSetting->set("nosuchgroup.test", "nothing");
    }

    public function test_given_a_string_when_setting_a_config_value_it_is_set()
    {
        $this->userSetting->set("interface.someRandomInterfaceProp", "worksForMe" );

        $this->assertEquals( "worksForMe", $this->userSetting['interface']['someRandomInterfaceProp'] );
    }

    public function test_given_a_string_property_exists_when_getting_a_config_value_it_is_returned()
    {
        $this->userSetting->interface = ["someRandomInterfaceProp" => "worksGreat"];

        $this->assertEquals( "worksGreat", $this->userSetting->get("interface.someRandomInterfaceProp" ) );
    }

    public function test_given_a_number_in_a_string_property_exists_when_getting_a_config_value_it_is_returned()
    {
        $this->userSetting->interface = ["someRandomInterfaceProp" => "200"];

        $this->assertSame( "200", $this->userSetting->get("interface.someRandomInterfaceProp" ) );
    }

    public function test_given_a_number_property_exists_when_getting_a_config_value_it_is_returned()
    {
        $this->userSetting->storage = ["someRandomStorageProp" => 242 ];

        $this->assertSame( 242, $this->userSetting->get("storage.someRandomStorageProp" ) );
    }

    public function test_given_a_bool_property_exists_when_getting_a_config_value_it_is_returned()
    {
        $this->userSetting->data = ["someRandomDataProp" => true ];

        $this->assertSame( true, $this->userSetting->get("data.someRandomDataProp" ) );
    }

    public function test_given_a_bool_when_setting_a_config_value_it_is_set()
    {
        $this->userSetting->set('workflow.someRandomWorkflowProp', false );

        $this->assertSame( false, $this->userSetting['workflow']['someRandomWorkflowProp'] );
    }

    public function test_given_a_number_when_setting_a_config_value_it_is_set()
    {
        $this->userSetting->set('storage.someRandomStorageProp', 42 );

        $this->assertSame( 42, $this->userSetting['storage']['someRandomStorageProp'] );
    }

    public function test_given_a_user_setting_and_it_has_no_interface_language_when_getting_the_interface_language_it_returns_the_default()
    {
        $this->assertEquals( "en", $this->userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_an_interface_language_when_getting_the_interface_language_it_is_returned()
    {
        $this->userSetting->interface = ["language" => "someLanguage"];

        $this->assertEquals( "someLanguage", $this->userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_no_interface_language_when_setting_the_interface_language_it_is_updated()
    {
        $this->userSetting->interfaceLanguage = "testLanguage";
        $this->assertEquals( "testLanguage", $this->userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_an_interface_language_when_setting_the_interface_language_it_is_updated()
    {
        $this->userSetting->interface = ["language" => "originalLanguage"];

        $this->userSetting->interfaceLanguage = "testLanguage";
        $this->assertEquals( "testLanguage", $this->userSetting->interfaceLanguage );
    }

    public function test_given_a_user_setting_and_it_has_a_default_aip_storage_location_when_getting_the_storage_location_it_is_returned()
    {
        $this->userSetting->storage = ["defaultAipStorageLocationId" => 20];

        $this->assertEquals( 20, $this->userSetting->defaultAipStorageLocationId );
    }

    public function test_given_a_user_setting_and_it_has_no_default_aip_storage_location_when_setting_the_storage_location_it_is_updated()
    {
        $this->userSetting->defaultAipStorageLocationId = 30;
        $this->assertEquals( 30, $this->userSetting->defaultAipStorageLocationId );
    }

    public function test_given_a_user_setting_and_it_has_a_default_dip_storage_location_when_getting_the_storage_location_it_is_returned()
    {
        $this->userSetting->storage = ["defaultDipStorageLocationId" => 40];

        $this->assertEquals( 40, $this->userSetting->defaultDipStorageLocationId );
    }

    public function test_given_a_user_setting_and_it_has_no_default_dip_storage_location_when_setting_the_storage_location_it_is_updated()
    {
        $this->userSetting->defaultDipStorageLocationId = 50;
        $this->assertEquals( 50, $this->userSetting->defaultDipStorageLocationId );
    }

    public function test_given_a_user_setting_and_it_has_no_ingest_compound_mode_enabled_value_set_it_returns_the_default()
    {
        $this->assertNotNull( $this->userSetting->ingestCompoundModeEnabled );
    }

    public function test_given_a_user_setting_and_it_has_the_ingest_compound_mode_enabled_value_set_to_true_it_returns_true()
    {
        $this->userSetting->workflow = [ "ingestCompoundModeEnabled" => true ];

        $this->assertTrue( $this->userSetting->ingestCompoundModeEnabled );
    }

    public function test_given_a_user_setting_and_it_has_the_ingest_compound_mode_enabled_value_set_to_false_it_returns_false()
    {
        $this->userSetting->workflow = [ "ingestCompoundModeEnabled" => false ];

        $this->assertFalse( $this->userSetting->ingestCompoundModeEnabled );
    }

    public function test_given_a_user_setting_and_it_has_the_ingest_compound_model_enabled_set_to_true_when_setting_it_to_false_then_it_is_false()
    {
        $this->userSetting->workflow = [ "ingestCompoundModeEnabled" => true ];

        $this->userSetting->ingestCompoundModeEnabled = false;
        $this->assertFalse( $this->userSetting->ingestCompoundModeEnabled );
    }

    public function test_given_a_user_setting_and_it_has_the_ingest_compound_model_enabled_set_to_false_when_setting_it_to_true_then_it_is_true()
    {
        $this->userSetting->workflow = [ "ingestCompoundModeEnabled" => false ];

        $this->userSetting->ingestCompoundModeEnabled = true;
        $this->assertTrue( $this->userSetting->ingestCompoundModeEnabled );
    }

}
