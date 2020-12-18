<?php

namespace Tests\Unit;

use App\Bag;
use App\File;
use App\Services\ArchivematicaDashboardClientService;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\Interfaces\ArchivematicaConnectionServiceInterface;
use App\Interfaces\ArchivematicaServiceConnectionInterface;
use App\Services\ArchivematicaServiceConnection;
use App\ArchivematicaService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;
use Laravel\Passport\Passport;

class ArchivematicaClientTest extends TestCase
{
    use DatabaseTransactions;
    protected $bag;

    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        Passport::actingAs( $user );
        $bag = factory(Bag::class)->create([
            "owner" => $user->id,
            "status" => "move_to_outbox"
        ]);

        $this->files = factory(File::class, 1)->create()->each(function($file) use ($bag) {
            $file->bag_id = $bag->id;
            $file->save();
        });

        $this->bag = $bag->fresh();
    }

    public function test_initiate_transfer()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'POST') &&
                ($uri == 'transfer/start_transfer/') &&
                ($options['form_params']['name'] == $this->bag->name) &&
                ($options['form_params']['paths[]'] != '') &&
                ($options['form_params']['row_ids[]'] == '')
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')->once()
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use ($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                 ->andReturns( $connnectionService );
        });
        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->initiateTransfer($this->bag->name, $this->bag->uuid, $this->bag->zipBagFileName());
    }

    public function test_get_unapproved_list()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'GET') &&
                ($uri == 'transfer/unapproved/') &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                 ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use ($connnectionService){
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                 ->andReturn( $connnectionService );
        });
        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->getUnapprovedList();
    }

    public function test_get_transfer_status()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'GET') &&
                ($uri == 'transfer/status/') &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                 ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->getTransferStatus();
    }

    public function test_get_transfer_status_with_uuid()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'GET') &&
                ($uri == 'transfer/status/'.$this->bag->uuid) &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->getTransferStatus($this->bag->uuid);
    }

    public function test_get_ingest_status()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'GET') &&
                ($uri == 'ingest/status/') &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->getIngestStatus();
    }

    public function test_get_ingest_status_with_uuid()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'GET') &&
                ($uri == 'ingest/status/'.$this->bag->uuid) &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });


        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->getIngestStatus($this->bag->uuid);
    }

    public function test_approve_transfer()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'POST') &&
                ($uri == 'transfer/approve/') &&
                ($options['form_params']['directory'] == $this->bag->zipBagFileName())
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->approveTransfer($this->bag->zipBagFileName());
    }

    public function test_hide_transfer_status()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'DELETE') &&
                ($uri == 'transfer/'.$this->bag->uuid.'/delete/') &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->hideTransferStatus($this->bag->uuid);
    }

    public function test_hide_ingest_status()
    {
        $closure = function($method, $uri, array $options = []) {
            return (
                ($method == 'DELETE') &&
                ($uri == 'ingest/'.$this->bag->uuid.'/delete/') &&
                ($options == [])
            );
        };

        $connnectionService = Mockery::mock( ArchivematicaServiceConnectionInterface::class, function( $mock ) use ($closure) {
            $mock->shouldReceive('doRequest')
                ->withArgs( $closure );
        });

        $this->mock(ArchivematicaConnectionServiceInterface::class, function( $mock ) use($connnectionService) {
            $mock->shouldReceive('getFirstAvailableDashboard')->once()
                ->andReturn( $connnectionService );
        });

        $amClient = resolve('App\Interfaces\ArchivematicaDashboardClientInterface');
        $amClient->hideIngestStatus( $this->bag->uuid );
    }

}
