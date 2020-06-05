<?php

namespace Tests\Unit;

use App\Enums\PermissionType;
use App\Permission;
use App\Services\PermissionManager;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionManagerTest extends TestCase
{
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
        $a = PermissionManager::createAction($g->id, 'Create Article', 'Write a new article');
        $a->save();
        $action = Permission::where('id', $a->id)->first();
        $this->assertNotNull($action);
        $this->assertEquals($action->type, PermissionType::Action);
        $this->assertEquals($action->id, $a->id);
        $this->assertEquals($action->parent_id, $g->id);
        $this->assertEquals($action->name, 'Create Article');
    }

    public function testCanDeletePermission()
    {
        $a = PermissionManager::createAction(null, 'Soome Action', 'I do stuff');
        $a->save();
        $this->assertNotNull($a);
        $action = Permission::where('id', $a->id)->first();
        $this->assertNotNull($action);
        PermissionManager::delete($action->id);
        $this->assertNull(Permission::where('id', $a->id)->first());
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
        $a = PermissionManager::createAction($g->id, 'Kill Software', 'Corrupts software');
        $a->save();
        PermissionManager::assignPermissionsToUsers([$a->id], [90]);
        $pm = PermissionManager::userHasPermission(90, $a->id);
        $this->assertTrue(count($pm) > 0);
        $pm = $pm[0];
        $this->assertEquals(90, $pm->user_id);
        $this->assertEquals($a->id, $pm->action_id);
        $this->assertEquals($g->id, $pm->group_id);
    }    
    
    public function testCanUnassignPermissionsFromUser()
    {
        $g = PermissionManager::createGroup('Transporter', 'Move things');
        $g->save();
        $a = PermissionManager::createAction($g->id, 'Drive', 'Use land locomotive');
        $a->save();
        
        PermissionManager::assignPermissionsToUsers([$a->id], [95]);
        $p1 = PermissionManager::userHasPermission(95, $a->id);
        $this->assertTrue(count($p1) > 0);
        $this->assertEquals($p1[0]->user_id, 95);
        PermissionManager::removePermissionsFromUsers([$a->id], [95]);

        $p2 = PermissionManager::userHasPermission(95, $a->id);
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
