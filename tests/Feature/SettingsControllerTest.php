<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;

class SettingsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
    }

    public function test_given_an_authenticated_user_when_getting_settings_it_responds_200()
    {
        $response = $this->actingAs( $this->testUser )
            ->get( route('web.showSettings') );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_posting_settings_it_responds_200()
    {
        $response = $this->actingAs( $this->testUser )
                         ->post( route('web.updateSettings', [ 
                         'interfaceLanguage' => 'en',
                         'ingestCompoundMode' => 'single',
                         'defaultAipStorageLocation' => 1,
                         'defaultDipStorageLocation' => 2 
                     ]));
        $response->assertStatus( 200 );
    }
}
