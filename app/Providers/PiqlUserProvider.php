<?php 

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Routing\UrlGenerator;
use SoapClient;
use App\User;
use Log;

class PiqlUserProvider extends EloquentUserProvider implements UserProvider 
{
    protected $model;

    private $acwsHostname = "http://testamu1.piql.com:8081";

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
        Log::info("retrieveByCredentials");
        $username = $credentials['username'];
        $password = $credentials['password'];
        $userId = $this->getUserId($username);
        $soapAuth = $this->soapClient($userId, $password);

        return $this->model;
	}

	public function validateCredentials(Authenticatable $user, array $credentials)
    {
        Log::info("validateCredentials");
        $username = $credentials['username'];
        $password = $credentials['password'];
        $userId = $this->getUserId($username);
        Log::info("username: ".$username." / userId: ".$userId."   password: ".$password);
        $soapAuth = $this->soapClient($userId, $password);
        if( isset($soapAuth["isAuthenticated"]) && $soapAuth["isAuthenticated"] )
        {
            return true;
        }
        return false;
	}

    private function getUserId(string $username)
    {
        if($this->model == null)
        {
            $this->model = User::findByUsername($username);
        }

        if($this->model == null)
        {
            return 0;
        }
        return $this->model->id;
    }

    private function soapClient(string $userId, string $password)
    {
        $acwsWsdlUrl = url("wsdl/ac.wsdl");
        $acwsAcClient = new SoapClient($acwsWsdlUrl, array('exceptions' => false, 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'connection_timeout' => 65, 'keep_alive' => false ));
        $acwsAcClient->__setLocation($this->acwsHostname);

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
                Log::warn('AuthenticateUsers failed: Could not authenticate user : '.$soapReturn['reason']);
            }
            else
            {
                Log::info('User with id '.$userId.' authenticated against '.$this->acwsHostname.'.');
            }
        }
        else
        {
            Log::error("SOAP Communication problem!");
            dump($soapReturn);
        }

        return $soapReturn;
    }
}
