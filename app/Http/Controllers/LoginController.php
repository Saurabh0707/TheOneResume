<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\User;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;


class LoginController extends ApiController
{
	use HasApiTokens;
    private $client;
    public function __construct()
    {
        
    }
    //function to retrieve the client credentials
    // public function getClient($client_id)
    // {
    //     //dynamically retrieving the client_id and secret
    //     $this->client=Client::find($client_id);
    // }
    /**
     * Login a user.
     *
     */
    public function login(Request $request)
    {
         $rules = [
                        'email'=>'required',
                        'password'=>'required|min:6',
                ];
        $this->validate($request,$rules);  
        $params=[
                'grant_type'=>'password',
                'client_id'=>request('client_id'),
                'client_secret'=>request('client_secret'),
                'username'=>request('email'),
                'password'=>request('password'),
                'scope'=>'*'
            ];
        $request->request->add($params);
        $makerequest=Request::create('oauth/token','POST');
        return Route::dispatch($makerequest);        
    }
}