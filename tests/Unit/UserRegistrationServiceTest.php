<?php

namespace Tests\Unit;

use App\Mail\ConfirmUserRegistration;
use App\Services\UserRegistrationService;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;

class UserRegistrationServiceTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_can_initiate_user_regstration()
    {
        Mail::fake();
        $usr = UserRegistrationService::registerUser('John Doe Mukiibi', 'jdoem', 'jdmk123@org.com', 'http://org.com') ;
        Mail::assertSent(ConfirmUserRegistration::class, function ($mail) use ($usr) {
            return $mail->user->id === $usr->id
                    && $mail->host === 'http://org.com'
                    && $mail->hasTo($usr->email);
        });
        Mail::assertSent(ConfirmUserRegistration::class, 1);
        $u = User::where('username', 'jdoem')->first();
        $this->assertNotNull($u->confirmation_token);
        $this->assertEquals('John Doe Mukiibi', $u->full_name);
        $this->assertNull($u->email_verified_at);    
    }

    public function test_cannot_initiate_duplicate_user_regstration()
    {
        UserRegistrationService::registerUser('John Dane Kikaabi', 'kdoem', 'kdmk123@org.com', 'http://org.com') ;
        $this->expectException("Exception");  
        UserRegistrationService::registerUser('John Dane Kikaabi', 'kdoem', 'kdmk123@org.com', 'http://org.com') ;
    }

    public function test_can_complete_user_regstration()
    {
        UserRegistrationService::registerUser('Jane Doess Masaaba', 'jndesm', 'jsdmk123fd@org.com', 'http://org.com') ;
        $c = User::where('username', 'jndesm')->first();
        UserRegistrationService::confirmUser($c->confirmation_token, "password123");
        $u = User::where('username', 'jndesm')->first();
        $this->assertNull($u->confirmation_token);
        $this->assertNotNull($u->email_verified_at);
        $this->assertEquals('password123', decrypt($u->password));
    }
}
