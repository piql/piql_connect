<?php

namespace Tests\Unit;

use App\Organization;
use App\Account;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp() : void
    {
        parent::setUp();
    }

    /**
     * @covers test create an organization and make sure an uuid is created
     * and name is created
     */
    public function test_create_organization() {
        $org = factory(Organization::class)->create();
        $this->assertTrue(strlen($org->name) > 0,
            "Organization doesn't have a valid name");
        $this->assertStringMatchesFormat("%x-%x-%x-%x-%x", $org->uuid,
            "Organization doesn't have a valid uuid");
    }

    public function test_find_account_from_organization() {
        $org = factory(Organization::class)->create();
        $acc = factory(Account::class)->create();
        $org->account()->save($acc);
        $this->assertEquals($org->account->uuid, $acc->uuid);
    }

    public function test_find_organization_from_account() {
        $org = factory(Organization::class)->create();
        $acc = factory(Account::class)->create();
        $org->account()->save($acc);
        $this->assertEquals($acc->organization->uuid, $org->uuid);
    }

    public function test_matching_user_lists_from_account_and_organization() {
        // NOTE: All account related asserts in this test must be deleted when account->users is refactored away     

        $org = factory(Organization::class)->create();
        $acc = factory(Account::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $org->account()->save($acc);
        $org->users()->save($userA);
        $org->users()->save($userB);

        $this->assertEquals($org->users->count(), 2);
        $this->assertEquals($acc->users->count(), 2);
        
        $orgUserIds = $org->users->pluck('id')->toArray();
        $this->assertContains($userA->id, $orgUserIds);
        $this->assertContains($userB->id, $orgUserIds);

        $accUserIds = $acc->users->pluck('id')->toArray();
        $this->assertContains($userA->id, $accUserIds);
        $this->assertContains($userB->id, $accUserIds);
    }
}
