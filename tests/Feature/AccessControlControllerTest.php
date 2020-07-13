<?php

namespace Tests\Feature;

use App\AccessControl;
use App\Services\AccessControlManager;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessControlControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listing_access_control_returns_200()
    {
        $response = $this->get('/api/v1/admin/access-control');
        $response->assertOk();
    }

    public function test_fetching_existing_access_control_by_id_returns_200()
    {
        $g = factory(AccessControl::class)->create();
        $this->assertNotNull($g);
        $response = $this->get('/api/v1/admin/access-control/' . $g->id);
        $response->assertOk();
        $AccessControl = $response->decodeResponseJson('data');
        $this->assertEquals($g->id, $AccessControl['id']);
    }

    public function test_fetching_non_existing_access_control_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/access-control/054321');
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }

    public function test_can_create_group()
    {
        $response = $this->json('post', '/api/v1/admin/access-control/groups', [
            'name' => 'Presidents', 'description' => 'Chaps that lead countries'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'Presidents');
    }

    public function test_can_add_role_to_group()
    {
        $g = AccessControlManager::createGroup('Ministers', 'Guys that eat national money');
        $this->assertTrue(true, $g->save());
        $response = $this->json('post', '/api/v1/admin/access-control/groups/'.$g->id.'/role', [
            'name' => 'MOH', 'description' => 'Ministry of Health'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'MOH');
    }

    public function test_can_assign_access_control_to_user()
    {
        $r = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/access-control/users/assign', [
            'users' => [$u1->id, $u2->id], 'access_controls'=>[$r->id]
        ]);
        $response->assertOk();
        $hp = AccessControlManager::userHasAccessControl($u1->id, $r->id);
        $this->assertEquals($r->id, $hp['role_id']);
        $this->assertEquals($u1->getIdAttribute(), $hp['user_id']);
    }    
    
    public function test_can_unassign_access_control_from_user()
    {
        $r = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/access-control/users/unassign', [
            'users' => [$u1->id], 'access_controls'=>[$r->id]
        ]);
        $response->assertOk();
        $hp = AccessControlManager::userHasAccessControl($u1->id, $r->id);
        $this->assertEquals($r->id, $hp['role_id']);
        $this->assertEquals(null, $hp['user_id']);
    }
    
    public function test_can_list_users_with_access_control()
    {
        $r = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create();
        $u3 = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$r->id], [$u1->id, $u2->id, $u3->id]);
        $response = $this->get('/api/v1/admin/access-control/'.$r->id.'/users');
        $response->assertOk();
        $this->assertEquals(3, count($response->decodeResponseJson("data")));
        
    }
}
