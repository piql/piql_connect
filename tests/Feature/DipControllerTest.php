<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;
use App\FileObject;

class DipControllerTest extends TestCase
{	
	public function setUp() : void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
		Passport::actingAs($this->user);
	}
	
	public function test_show_file_returns_200()
    {
    	$fileObject = factory(FileObject::class)->create(['fullpath'=>'xxxx', 'filename'=>'xxxxx', 'path'=>'xxxx', 'size'=>123, 'object_type'=>'file', 'info_source'=>'connect', 'mime_type'=>'text/xml', 'storable_type'=>'App\Dip', 'storable_id'=>1]);
    	$response = $this->get('/api/v1/access/dips/files/' . $fileObject->id);
        $response->assertOk();
        $resp = $response->decodeResponseJson();
        $this->assertEquals($fileObject->id, $resp['id']);
        $fileObject->delete();
    }
}
