<?php

namespace Tests\Feature;

use App\Bag;
use App\Jobs\ProcessArchivematicaServiceCallback;
use App\Listeners\ArchivematicaServiceConnection;
use App\Services\ArchivematicaConnectionService;
use App\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;
use Log;

class ArchivematicaServiceCallbackTest extends TestCase
{
    use DatabaseTransactions;
    private $bag;
    private $packageUuid;
    private $serviceUuid;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $user = factory(User::class)->create();
        $this->bag = factory(Bag::class)->create([
            "owner" => $user->id,
            "status" => "initiate_transfer"
        ]);

        $this->packageUuid = 'c2f55dcc-304a-4b04-81a4-fac91fbdac54';
        $this->serviceUuid = '913860d0-020c-11ea-bcdc-d372f426b83d';

    }

    private function getFileDetailsResponse($packageUuid, $bagUuid, $statusCode = 200, $packageType = "AIP") {
        $pattern = '/(\w{4})(\w{4})-(\w{4})-(\w{4})-(\w{4})-(\w{4})(\w{4})(\w{4})/';
        $replacement = '$1/$2/$3/$4/$5/$6/$7/$8';
        $path = preg_replace($pattern, $replacement, $packageUuid);
        $response = json_decode( '{
          "current_full_path": "/var/archivematica/sharedDirectory/www/AIPsStore/'. $path .'/H01-'. $bagUuid .'-'. $packageUuid .'.7z",
          "current_location": "/api/v2/location/ea71f8da-415d-4a2d-8103-f02066a54e2f/",
          "current_path": "'. $path .'/H01-'. $bagUuid .'-'. $packageUuid .'.7z",
          "encrypted": false,
          "misc_attributes": {},
          "origin_pipeline": "/api/v2/pipeline/fdca7d1e-14a7-4f62-b9e2-22f3b6771db9/",
          "package_type": "'. $packageType .'",
          "related_packages": [
        "/api/v2/file/57e2f036-dcf1-464f-a244-159eb6faa2c1/"
    ],
          "replicas": [],
          "replicated_package": null,
          "resource_uri": "/api/v2/file/'. $packageUuid .'/",
          "size": 20564,
          "status": "UPLOADED",
          "uuid": "'. $packageUuid .'"
        }');
        return (object)[
            'contents' => ($statusCode == 200) ? $response : "",
            'statusCode' => $statusCode,
        ];
    }

    public function test_with_valid_package_uuid_and_service_uuid_and_archivematica_returns_valid_AIP_description()
    {
        $serviceConnection = Mockery::mock(ArchivematicaServiceConnection::class);
        $serviceConnection->shouldReceive('doRequest')->once()->andReturns(
            $this->getFileDetailsResponse($this->packageUuid, $this->bag->uuid, 200)
        );

        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn($serviceConnection);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^AIP uuid /'));
        Log::shouldReceive('error')->times(0);

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals($this->packageUuid, $this->bag->storage_properties->aip_uuid);
    }

    public function test_with_valid_package_uuid_and_service_uuid_and_archivematica_returns_valid_DIP_description()
    {
        $serviceConnection = Mockery::mock(ArchivematicaServiceConnection::class);
        $serviceConnection->shouldReceive('doRequest')->once()->andReturns(
            $this->getFileDetailsResponse($this->packageUuid, $this->bag->uuid, 200, 'DIP')
        );

        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn($serviceConnection);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^DIP uuid /'));
        Log::shouldReceive('error')->times(0);

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals($this->packageUuid, $this->bag->storage_properties->dip_uuid);
    }

    public function test_with_valid_package_uuid_and_service_uuid_and_archivematica_returns_invalid_package_type()
    {
        $serviceConnection = Mockery::mock(ArchivematicaServiceConnection::class);
        $serviceConnection->shouldReceive('doRequest')->once()->andReturns(
            $this->getFileDetailsResponse($this->packageUuid, $this->bag->uuid, 200, 'x')
        );

        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn($serviceConnection);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Unsupported package type/'));

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals(null, $this->bag->storage_properties->aip_uuid);
        self::assertEquals(null, $this->bag->storage_properties->dip_uuid);
    }

    public function test_with_invalid_package_uuid_and_service_uuid_and_archivematica_returns_404()
    {
        $serviceConnection = Mockery::mock(ArchivematicaServiceConnection::class);
        $serviceConnection->shouldReceive('doRequest')->once()->andReturns(
            $this->getFileDetailsResponse($this->packageUuid, $this->bag->uuid, 404)
        );

        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn($serviceConnection);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Get file details failed with error code/'));

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals("", $this->bag->storage_properties->aip_uuid);

    }

    public function test_with_valid_package_uuid_and_invalid_service_uuid()
    {
        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn(null);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^No service found with uuid/'));

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals("", $this->bag->storage_properties->aip_uuid);

    }

    public function test_archivematica_returns_with_no_reference_bag_uuid()
    {
        $serviceConnection = Mockery::mock(ArchivematicaServiceConnection::class);
        $serviceConnection->shouldReceive('doRequest')->once()->andReturns(
            $this->getFileDetailsResponse($this->packageUuid, '57e2f036-dcf1-464f-a244-159eb6faa2c1', 200)
        );

        $connectionService = Mockery::mock(ArchivematicaConnectionService::class);
        $connectionService->shouldReceive('getServiceConnectionByUuid')->once()->andReturn($serviceConnection);

        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Processing uploaded package with uuid:/'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Could not find any storage properties linked to this uuid/'));

        $job = new ProcessArchivematicaServiceCallback($this->serviceUuid, $this->packageUuid);
        $job->handle($connectionService);

        self::assertEquals(null, $this->bag->storage_properties->aip_uuid);
    }

}
