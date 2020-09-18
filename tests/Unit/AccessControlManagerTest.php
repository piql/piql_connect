<?php

namespace Tests\Unit;

use App\Enums\AccessControlType;
use App\AccessControl;
use App\Services\AccessControlManager;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessControlManagerTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCanCreatePermissionGroup()
    {
        $g = AccessControlManager::createPermissionGroup('Group 1', 'A test group');
        $g->save();
        $group = AccessControl::where('id', $g->id)->first();
        $this->assertNotNull($group);
        $this->assertEquals($group->type, AccessControlType::PermissionGroup);
        $this->assertEquals($group->id, $g->id);
        $this->assertEquals($group->name, 'Group 1');
    
    }
    
    public function testCanCreatePermission()
    {
        $g = AccessControlManager::createPermissionGroup('Author', 'People that write stuff');
        $g->save();
        $this->assertNotNull($g);
        $r = AccessControlManager::createPermission($g->id, 'Create Article', 'Write r new article');
        $r->save();
        $role = AccessControl::where('id', $r->id)->first();
        $this->assertNotNull($role);
        $this->assertEquals($role->type, AccessControlType::Permission);
        $this->assertEquals($role->id, $r->id);
        $this->assertEquals($role->group_id, $g->id);
        $this->assertEquals($role->name, 'Create Article');
    }

    public function testCanDeleteAccessControl()
    {
        $r = AccessControlManager::createPermission(null, 'Some Role', 'I do stuff');
        $r->save();
        $this->assertNotNull($r);
        $role = AccessControl::where('id', $r->id)->first();
        $this->assertNotNull($role);
        AccessControlManager::delete($role->id);
        $this->assertNull(AccessControl::where('id', $r->id)->first());
    } 
    
    // public function testGroupDeletionAlsoDeletesPermissions()
    // {
    //     $g = AccessControlManager::createPermissionGroup('Teacher', 'People that educate');
    //     $g->save();
    //     $a1 = AccessControlManager::createPermission($g->id, 'Teach', 'Pass on Knowledge');
    //     $a1->save();
    //     $a2 = AccessControlManager::createPermission($g->id, 'Evaluate Exam', 'Mark exams and tests');
    //     $a2->save();
    //     $this->assertNotNull(AccessControl::where('id', $g->id)->first());
    //     $this->assertNotNull(AccessControl::where('id', $a1->id)->first());
    //     $this->assertNotNull(AccessControl::where('id', $a2->id)->first());
    //     AccessControlManager::delete($g->id);
    //     $this->assertNull(AccessControl::where('id', $g->id)->first());
    //     $this->assertNull(AccessControl::where('id', $a1->id)->first());
    //     $this->assertNull(AccessControl::where('id', $a2->id)->first());
    // } 
    
    public function testCanAssignAccessControlsToUser()
    {
        $g = AccessControlManager::createPermissionGroup('Virus', 'Program destroyer');
        $g->save();
        $r = AccessControlManager::createPermission($g->id, 'Kill Software', 'Corrupts software');
        $r->save();
        $u = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$r->id], [$u->id]);
        $pm = AccessControlManager::userHasAccessControl($u->id, $r->id);
        $this->assertTrue(count($pm) > 0);
        $this->assertEquals($u->id, $pm['user_id']);
        $this->assertEquals($r->id, $pm['permission_id']);
        $this->assertEquals($g->id, $pm['group_id']);
    }

    public function testCanAssignRolesToUser()
    {
        $g = AccessControlManager::createPermissionGroup('Virus', 'Program destroyer');
        $g->save();
        $r = AccessControlManager::createPermission($g->id, 'Kill Software', 'Corrupts software');
        $r->save();
        $u = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$r->id], [$u->id]);
        $pm = AccessControlManager::userHasAccessControl($u->id, $r->id);
        $this->assertTrue(count($pm) > 0);
        $this->assertEquals($u->id, $pm['user_id']);
        $this->assertEquals($r->id, $pm['permission_id']);
        $this->assertEquals($g->id, $pm['group_id']);
    }
    
    public function testCanUnassignAccessControlsFromUser()
    {
        $g = AccessControlManager::createPermissionGroup('Transporter', 'Move things');
        $g->save();
        $r = AccessControlManager::createPermission($g->id, 'Drive', 'Use land locomotive');
        $r->save();
        
        $u = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$r->id], [$u->id]);
        $p1 = AccessControlManager::userHasAccessControl($u->id, $r->id);
        $this->assertFalse(empty($p1));
        $this->assertEquals($p1['user_id'], $u->id);
        AccessControlManager::removeAccessControlsFromUsers([$r->id], [$u->id]);

        $p2 = AccessControlManager::userHasAccessControl($u->id, $r->id);
        $this->assertEmpty($p2);
    }
    
    public function testAccessControlDeletionAlsoDeletesUserAssignment()
    {
        $g = AccessControlManager::createPermissionGroup('Virus', 'Program destroyer');
        $g->save();
        $a1 = AccessControlManager::createPermission($g->id, 'Kill Software', 'Corrupts software');
        $a1->save();
        $a2 = AccessControlManager::createPermission($g->id, 'Kill Hardware', 'Corrupts hardware');
        $a2->save();
        $u = factory(User::class)->create();
        AccessControlManager::assignAccessControlsToUsers([$a2->id], [$u->id]);
        $this->assertEquals($u->id, AccessControlManager::userHasAccessControl($u->id, $a2->id)['user_id']);
        AccessControlManager::delete($g->id);
        $this->assertEmpty(AccessControlManager::userHasAccessControl($u->id, $a2->id));
    }
}
