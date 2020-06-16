<?php

namespace Tests\Unit;

use App\Enums\PermissionType;
use App\Permission;
use App\Services\PermissionManager;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionManagerTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCanCreateGroup()
    {
        $g = PermissionManager::createGroup('Group 1', 'A test group');
        $g->save();
        $group = Permission::where('id', $g->id)->first();
        $this->assertNotNull($group);
        $this->assertEquals($group->type, PermissionType::Group);
        $this->assertEquals($group->id, $g->id);
        $this->assertEquals($group->name, 'Group 1');
    
    }
    
    public function testCanCreateAction()
    {
        $g = PermissionManager::createGroup('Author', 'People that write stuff');
        $g->save();
        $this->assertNotNull($g);
        $r = PermissionManager::createAction($g->id, 'Create Article', 'Write r new article');
        $r->save();
        $role = Permission::where('id', $r->id)->first();
        $this->assertNotNull($role);
        $this->assertEquals($role->type, PermissionType::Role);
        $this->assertEquals($role->id, $r->id);
        $this->assertEquals($role->parent_id, $g->id);
        $this->assertEquals($role->name, 'Create Article');
    }

    public function testCanDeletePermission()
    {
        $r = PermissionManager::createAction(null, 'Some Role', 'I do stuff');
        $r->save();
        $this->assertNotNull($r);
        $role = Permission::where('id', $r->id)->first();
        $this->assertNotNull($role);
        PermissionManager::delete($role->id);
        $this->assertNull(Permission::where('id', $r->id)->first());
    } 
    
    public function testGroupDeletionAlsoDeletesActions()
    {
        $g = PermissionManager::createGroup('Teacher', 'People that educate');
        $g->save();
        $a1 = PermissionManager::createAction($g->id, 'Teach', 'Pass on Knowledge');
        $a1->save();
        $a2 = PermissionManager::createAction($g->id, 'Evaluate Exam', 'Mark exams and tests');
        $a2->save();
        $this->assertNotNull(Permission::where('id', $g->id)->first());
        $this->assertNotNull(Permission::where('id', $a1->id)->first());
        $this->assertNotNull(Permission::where('id', $a2->id)->first());
        PermissionManager::delete($g->id);
        $this->assertNull(Permission::where('id', $g->id)->first());
        $this->assertNull(Permission::where('id', $a1->id)->first());
        $this->assertNull(Permission::where('id', $a2->id)->first());
    } 
    
    public function testCanAssignPermissionsToUser()
    {
        $g = PermissionManager::createGroup('Virus', 'Program destroyer');
        $g->save();
        $r = PermissionManager::createAction($g->id, 'Kill Software', 'Corrupts software');
        $r->save();
        $u = factory(User::class)->create();
        PermissionManager::assignPermissionsToUsers([$r->id], [$u->id]);
        $pm = PermissionManager::userHasPermission($u->id, $r->id);
        $this->assertTrue(count($pm) > 0);
        $pm = $pm[0];
        $this->assertEquals($u->id, $pm->user_id);
        $this->assertEquals($r->id, $pm->role_id);
        $this->assertEquals($g->id, $pm->group_id);
    }    
    
    public function testCanUnassignPermissionsFromUser()
    {
        $g = PermissionManager::createGroup('Transporter', 'Move things');
        $g->save();
        $r = PermissionManager::createAction($g->id, 'Drive', 'Use land locomotive');
        $r->save();
        
        $u = factory(User::class)->create();
        PermissionManager::assignPermissionsToUsers([$r->id], [$u->id]);
        $p1 = PermissionManager::userHasPermission($u->id, $r->id);
        $this->assertTrue(count($p1) > 0);
        $this->assertEquals($p1[0]->user_id, $u->id);
        PermissionManager::removePermissionsFromUsers([$r->id], [$u->id]);

        $p2 = PermissionManager::userHasPermission($u->id, $r->id);
        $this->assertTrue(count($p2) > 0);
        $this->assertFalse(isset($p2[0]->user_id));
    }
    
    public function testPermissionDeletionAlsoDeletesUserAssignment()
    {
        $g = PermissionManager::createGroup('Virus', 'Program destroyer');
        $g->save();
        $a1 = PermissionManager::createAction($g->id, 'Kill Software', 'Corrupts software');
        $a1->save();
        $a2 = PermissionManager::createAction($g->id, 'Kill Hardware', 'Corrupts hardware');
        $a2->save();
        PermissionManager::assignPermissionsToUsers([$a2->id], [102]);
        $this->assertTrue(count(PermissionManager::userHasPermission(102, $a2->id)) > 0);
        PermissionManager::delete($g->id);
        $this->assertTrue(count(PermissionManager::userHasPermission(102, $a2->id)) == 0);
    }
}
