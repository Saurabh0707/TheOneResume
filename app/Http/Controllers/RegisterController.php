<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\User;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class RegisterController extends ApiController
{
	use HasApiTokens;
    private $client;
    public function __construct()
    {

    }
    //function to retrieve the client credentials
    public function getClient($client_id)
    {
        //dynamically retrieving the client_id and secret
        $this->client=Client::find($client_id);
    }
     /**
     * Register a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $rules = [
                        'name'=>'required',
                        'email'=>'required|email|unique:users,email',
                        'password'=>'required|min:6|confirmed',
                ];
        $this->validate($request,$rules);
        $this->getClient(request('client_id'));
        $user = User::Create($request->all());
        $params=[
            'grant_type'=>'password',
            'client_id'=>$this->client->id,
            'client_secret'=>$this->client->secret,
            'username'=>request('email'),
            'password'=>request('password'),
            'scope'=>'*'
        ];

        $request->request->add($params);
        $makerequest=Request::create('oauth/token','POST');
        return Route::dispatch($makerequest);       
    }
}