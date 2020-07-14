<?php

namespace Tests\Feature;

use App\AccessControl;
use App\Enums\AccessControlType;
use App\Services\AccessControlManager;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Generator;
use Faker\Provider\en_US\Text as TextFaker;

class AccessControlControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected TextFaker $text;

    protected function setUp() :void {
        parent::setUp();
        $this->text  = new TextFaker(new Generator());
    }

    public function test_listing_permission_permissions_returns_200()
    {
        $response = $this->get('/api/v1/admin/access-control/permissions');
        $response->assertOk();
    }    
    
    public function test_listing_permission_roles_returns_200()
    {
        $response = $this->get('/api/v1/admin/access-control/roles');
        $response->assertOk();
    }

    public function test_listing_permission_permission_groups_returns_200()
    {
        $response = $this->get('/api/v1/admin/access-control/permission-groups');
        $response->assertOk();
    }

    public function test_fetching_existing_permission_by_id_returns_200()
    {
        $text = $this->text;
        $g = AccessControlManager::createPermission(null, $text->realText(10), $text->realText(40));
        $this->assertTrue($g->save());
        $this->assertNotNull($g);
        $response = $this->get('/api/v1/admin/access-control/permissions/' . $g->id);
        $response->assertOk();
        $AccessControl = $response->decodeResponseJson('data');
        $this->assertEquals($g->id, $AccessControl['id']);
    }

    public function test_fetching_non_existing_permission_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/access-control/permissions/98854321');
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }

    public function test_can_create_permission_group()
    {
        $response = $this->json('post', '/api/v1/admin/access-control/permission-groups', [
            'name' => 'Presidents', 'description' => 'Chaps that lead countries'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'Presidents');
    }

    public function test_can_add_permission_to_permission_group()
    {
        $g = AccessControlManager::createPermissionGroup('Ministers', 'Guys that eat national money');
        $this->assertTrue(true, $g->save());
        $response = $this->json('post', '/api/v1/admin/access-control/permission-groups/'.$g->id.'/permission', [
            'name' => 'MOH', 'description' => 'Ministry of Health'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $g = $response->decodeResponseJson('data');
        $this->assertEquals($g['name'], 'MOH');
    }

    public function test_can_assign_permission_to_user()
    {
        $p = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/access-control/users/assign', [
            'users' => [$u1->id, $u2->id], 'access_controls'=>[$p->id]
        ]);
        $response->assertOk();
        $hp = AccessControlManager::userHasAccessControl($u1->id, $p->id);
        $this->assertEquals($p->id, $hp['permission_id']);
        $this->assertEquals($u1->getIdAttribute(), $hp['user_id']);
    }  
    
    // public function test_can_assign_role_to_user()
    // {
    //     $acm = new AccessControlManager();
    //     $text = $this->text;
    //     $g = $acm->createPermissionGroup($text->realText(10), $text->realText(40));
    //     $this->assertTrue($g->save());
    //     $p1 = $acm->createPermission($g->id, $text->realText(10), $text->realText(40)); 
    //     $p2 = $acm->createPermission($g->id, $text->realText(10), $text->realText(40)); 
    //     $r1 = $acm->createRole($text->realText(10), $text->realText(40)); 
    //     $r2 = $acm->createRole($text->realText(10), $text->realText(40));
    //     if($p1->save() && $p2->save() && $r1->save() && $r2->save())
    //         $acm->addPermissionsToRole($r1->id, [$g->id]);
    //     $u1 = factory(User::class)->create();
    //     $u2 = factory(User::class)->create();
    //     $response = $this->json('post', '/api/v1/admin/access-control/users/assign', [
    //         'users' => [$u1->id, $u2->id], 'access_controls'=>[$r1->id]
    //     ]);
    //     $response->assertOk();        
    //     $uhac = function($userId, $acId) use($acm) {
    //         return $acm->userHasAccessControl($userId, $acId);
    //     };
    //     var_dump($uhac($u1->id, $r1->id));
    //     $this->assertEquals($p1->id, $uhac($u1->id, $p1->id)['permission_id']);
    //     $this->assertEquals($p2->id, $uhac($u1->id, $p2->id)['permission_id']);
    //     $this->assertEquals($r1->id, $uhac($u1->id, $r1->id)['permission_id']);
    //     // $hp = AccessControlManager::userHasAccessControl($u1->id, $p->id);
    //     // $this->assertEquals($p->id, $hp['permission_id']);
    //     // $this->assertEquals($u1->getIdAttribute(), $hp['user_id']);
    // } 
    
    public function test_can_unassign_permission_from_user()
    {
        $p = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $response = $this->json('post', '/api/v1/admin/access-control/users/unassign', [
            'users' => [$u1->id], 'access_controls'=>[$p->id]
        ]);
        $response->assertOk();
        $hp = AccessControlManager::userHasAccessControl($u1->id, $p->id);
        $this->assertEquals($p->id, $hp['permission_id']);
        $this->assertEquals(null, $hp['user_id']);
    }
    
    public function test_can_list_users_with_permissions()
    {
        $p = factory(AccessControl::class)->create();
        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create();
        $u3 = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$p->id], [$u1->id, $u2->id, $u3->id]);
        $response = $this->get('/api/v1/admin/access-control/permissions/'.$p->id.'/users');
        $response->assertOk();
        $this->assertEquals(3, count($response->decodeResponseJson("data")));
        
    }
}
