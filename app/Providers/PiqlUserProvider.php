<?php 

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Hash;
use SoapClient;
use App\User;
use Log;

class PiqlUserProvider extends EloquentUserProvider implements UserProvider 
{
    public function __construct()
    {
    }

    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        Log::info("retrieveByToken");
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        Log::info("updateRememberToken");
    }

    public function retrieveByCredentials(array $credentials)
    {
        time_nanosleep(0,100000000); //slightly hamper brute force attacks
        if (isset($credentials['email']))
        {
            return $user = User::findByEmail($credentials['email']);
        }
        else if (isset($credentials['username']))
        {
            return $user = User::findByUsername($credentials['username']);
        }

        Log::error('Credentials were not defined');
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if( env("AMU_SOAP_AUTH_ENABLED") == "true" )
        {
            $soapAuth = $this->soapClient($user->id, $credentials['password']);
            return (isset($soapAuth["isAuthenticated"]) && $soapAuth["isAuthenticated"] );
        }
        else
        { 
            if(Hash::check( $credentials['password'], $user->password ))
            {
                Log::info('User `'.$user->username.'` authenticated against local database');
                return true;
            }
        }

        Log::notice('Authentication failure for user `'.$user->username.'`');

        return false;
    }

    private function soapClient(string $userId, string $password)
    {
        $acwsHostname = env("AMU_ACWS_PROTOCOL")."://".env("AMU_ACWS_HOST").":".env("AMU_ACWS_SOAP_PORT");
        $acwsWsdlUrl = url(env("AMU_WSDL_PATH"));

        $acwsAcClient = new SoapClient($acwsWsdlUrl, array('exceptions' => false, 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'connection_timeout' => 65, 'keep_alive' => false ));
        $acwsAcClient->__setLocation($acwsHostname);

        $soapReturn = (Array)$acwsAcClient->authenticateUser($userId, $password);
        if(is_soap_fault($soapReturn))
        {
            Log::error('AuthenticateUsers failed: Could not reach SOAP Service');
            return ['isAuthenticated' => false, 'reason' => 'SERVICE_UNAVAILABLE'];
        }
        
        if( isset($soapReturn['isAuthenticated']) )
        {
            if( $soapReturn['isAuthenticated'] == false )
            {
                Log::notice('AMU authentication failure for user with id '.$userId.': '.$soapReturn['reason']);
            }
            else
            {
                Log::info('User with id `'.$userId.'` authenticated against '.$acwsHostname.'.');
            }
        }
        else
        {
            Log::error("SOAP Communication problem!");
        }

        return $soapReturn;
    }
}
