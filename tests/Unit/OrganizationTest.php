<?php

namespace Tests\Unit;

use App\Events\OrganizationCreatedEvent;
use App\Organization;
use App\Archive;
use App\User;
use Faker\Factory as Faker;
use Faker\Provider\Address;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp() : void
    {
        parent::setUp();
    }

    /**
     * @test create a basic organization mass assign name
     */
    public function test_create_a_basic_organization_set_name_on_create() {
        $faker = Faker::create();

        $organizationName = $faker->company." ".$faker->companySuffix;
        $org = Organization::create([
            "name" => $organizationName,
        ]);
        $this->assertEquals($organizationName, $org->name,
            "Organization doesn't have a valid name");

    }

    /**
     * @test create a basic organization and test a valid uuid is created
     */
    public function test_create_a_basic_organization_and_test_a_valid_uuid_is_created() {
        $faker = Faker::create();

        $org = Organization::create([
            "name" => $faker->company." ".$faker->companySuffix,
        ]);

        // test a valid uuid is auto generated
        $this->assertStringMatchesFormat("%x-%x-%x-%x-%x", $org->uuid,
            "Organization doesn't have a valid uuid");

    }

    /**
     * @test create a basic organization mass assign name and uuid and validate uuid is not mass assignable
     */
    public function test_create_a_basic_organization_and_validate_uuid_is_not_mass_assignable() {
        $faker = Faker::create();

        $organizationName = $faker->company." ".$faker->companySuffix;
        $uuid = $faker->uuid;
        $org = Organization::create([
            "name" => $organizationName,
            "uuid" => $uuid
        ]);
        $this->assertEquals($organizationName, $org->name,
            "Organization doesn't have a valid name");

        // test a valid uuid is auto generated
        $this->assertStringMatchesFormat("%x-%x-%x-%x-%x", $org->uuid,
            "Organization doesn't have a valid uuid");

        // Mass assignment rule prevents setting UUID
        $this->assertNotEquals($org->uuid, $uuid,
            "Mass assignment rule didn't prevent setting UUID");
    }

    public function test_create_a_basic_organization_without_setting_a_name() {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Organization::create();
    }

    /**
     * @test create an organization and make sure an OrganizationCreatedEvent is sent
     */
    public function test_create_organization_and_validate_OrganizationCreatedEvent_is_sent()
    {
        $event = Event::fake();

        $org = factory(Organization::class)->create();
        $event->assertDispatched(OrganizationCreatedEvent::class,
            function (OrganizationCreatedEvent $event) use ($org) {
                return $org->uuid == $event->organization->uuid;
            });
    }

    public function test_find_archive_from_organization() {
        $org = factory(Organization::class)->create();
        $acc = factory(Archive::class)->create();
        $org->archive()->save($acc);
        $this->assertEquals($org->archive->uuid, $acc->uuid);
    }

    public function test_find_organization_from_archive() {
        $org = factory(Organization::class)->create();
        $acc = factory(Archive::class)->create();
        $org->archive()->save($acc);
        $this->assertEquals($acc->organization->uuid, $org->uuid);
    }

    public function test_user_lists_from_organization() {
        $org = factory(Organization::class)->create();
        $acc = factory(Archive::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $org->archive()->save($acc);
        $org->users()->save($userA);
        $org->users()->save($userB);

        $this->assertEquals($org->users->count(), 2);

        $orgUserIds = $org->users->pluck('id')->toArray();
        $this->assertContains($userA->id, $orgUserIds);
        $this->assertContains($userB->id, $orgUserIds);
    }
}
