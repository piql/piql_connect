<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs($this->user);
    }

    public function test_listing_users_returns_200()
    {
        $response = $this->get('/api/v1/admin/users');
        $response->assertOk();
    }
    
    public function test_fetching_existing_user_by_id_returns_200()
    {
        $u = factory(User::class)->create();
        $this->assertNotNull($u);
        $response = $this->get('/api/v1/admin/users/'.$u->getIdAttribute());
        $response->assertOk();
        $user = $response->decodeResponseJson('data');
        $this->assertEquals($u->id, $user['id']);

    }  
    
    public function test_fetching_non_existing_user_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/users/'.Str::uuid());
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }    
}
