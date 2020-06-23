<?php

namespace Tests\Feature;

use App\Permission;
use App\Services\PermissionManager;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listing_permissions_returns_200()
    {
        $response = $this->get('/api/v1/admin/permissions');
        $response->assertOk();
    }

    public function test_fetching_existing_permission_by_id_returns_200()
    {
        $g = factory(Permission::class)->create();
        $this->assertNotNull($g);
        $response = $this->get('/api/v1/admin/permissions/' . $g->id);
        $response->assertOk();
        $permission = $response->decodeResponseJson('data');
        $this->assertEquals($g->id, $permission['id']);
    }

    public function test_fetching_non_existing_permission_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/permissions/12345');
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }

    public function test_can_create_group()
    {
        $response = $this->json('post', '/api/v1/admin/permissions/groups', [
            'name' => 'Presidents', 'description' => 'Chaps that lead countries'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'Presidents');
    }

    public function test_can_add_role_to_group()
    {
        $g = PermissionManager::createGroup('Ministers', 'Guys that eat national money');
        $this->assertTrue(true, $g->save());
        $response = $this->json('post', '/api/v1/admin/permissions/groups/'.$g->id.'/role', [
            'name' => 'MOH', 'description' => 'Ministry of Health'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'MOH');
    }

    public function test_can_assign_user_to_permission()
    {
        $r = factory(Permission::class)->create();
        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/permissions/users/assign', [
            'users' => [$u1->id, $u2->id], 'permissions'=>[$r->id]
        ]);
        $response->assertOk();
        $hp = PermissionManager::userHasPermission($u1->id, $r->id);
        $this->assertEquals($r->id, $hp->role_id);
        $this->assertEquals($u1->getIdAttribute(), $hp->user_id);
    }    
    
    public function test_can_unassign_user_from_permission()
    {
        $r = factory(Permission::class)->create();
        $u1 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/permissions/users/unassign', [
            'users' => [$u1->id], 'permissions'=>[$r->id]
        ]);
        $response->assertOk();
        $hp = PermissionManager::userHasPermission($u1->id, $r->id);
        $this->assertEquals($r->id, $hp->role_id);
        $this->assertEquals(null, $hp->user_id);
    }
}
