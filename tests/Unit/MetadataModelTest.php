<?php

namespace Tests\Unit;

use App\Bag;
use App\EventLogEntry;
use App\File;
use App\Metadata;
use App\User;
use App\Account;
use App\Organization;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\UserSetting;

class MetadataModelTest extends TestCase
{
    private $user;
    private $file;

    public function setUp() : void
    {
        parent::setUp();
    }

    use DatabaseTransactions;

    public function test_associating_file_model_to_a_metadata_model()
    {
        $organization = factory( Organization::class )->create();
        factory( Account::class )->create(['organization_uuid' => $organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $organization->uuid]);
        Passport::actingAs( $this->user );

        $bag = factory(Bag::class)->create([
            "status" => "open"
        ]);
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);

        $this->file = factory(File::class)->create([
            "bag_id" => $bag->id,
        ]);

        $this->bag = $bag->fresh();

        $metadata->parent()->associate($this->file);
        $metadata->save();
        $metadata->fresh();
        $this->file->fresh();
        $this->assertEquals($this->file->metadata[0]->id, $metadata->id);
    }

    public function test_creating_metadata_model_where_owner_and_owner_type_are_provided()
    {
        $organization = factory( Organization::class )->create();
        $account = factory( Account::class )->create(['organization_uuid' => $organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $organization->uuid]);
        Passport::actingAs( $this->user );

        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
            "owner_id" => $account->uuid,
            "owner_type" => 'App\Account',
        ]);

        $this->assertNotEquals(null, $metadata);
    }

    public function test_dissociating_file_model_from_a_metadata_model()
    {
        $this->test_associating_file_model_to_a_metadata_model();

        $metadata = $this->file->metadata[0];
        $metadata->parent()->dissociate();
        $metadata->save();
        $this->assertEquals(0, count($this->file->refresh()->metadata));
    }

}
