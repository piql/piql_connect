<?php

namespace Tests\Unit;

use App\Bag;
use App\EventLogEntry;
use App\File;
use App\Metadata;
use App\User;
use App\Account;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\UserSetting;

class MetadataModelTest extends TestCase
{
    private $account;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
    }

    use DatabaseTransactions;

    public function test_associating_file_model_to_a_metadata_model()
    {
        $this->account = factory(Account::class)->create();
        $this->user = factory(User::class)->create();
        $this->user->account()->associate( $this->account );
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


    public function test_dissociating_file_model_from_a_metadata_model()
    {
        $this->test_associating_file_model_to_a_metadata_model();

        $metadata = $this->file->metadata[0];
        $metadata->parent()->dissociate();
        $metadata->save();
        $this->assertEquals(0, count($this->file->refresh()->metadata));
    }

}
