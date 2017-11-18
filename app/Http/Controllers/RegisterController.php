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

class RegisterController extends ApiController
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
     * Register a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $rules = [
                        'registerName'=>'required',
                        'registerEmail'=>'required|email|unique:users,email',
                        'registerPwd'=>'required|min:6',
                ];
        $this->validate($request,$rules);
        $user = User::create([
            'name' => $request['registerName'],
            'email' => $request['registerEmail'],
            'password' => bcrypt($request['registerPwd']),
            ]);
        $params=[
            'grant_type'=>'password',
            'client_id'=>request('client_id'),
            'client_secret'=>request('client_secret'),
            'username'=>request('registerEmail'),
            'password'=>request('registerPwd'),
            'scope'=>'*'
        ];

        $request->request->add($params);
        $makerequest=Request::create('oauth/token','POST');
        return Route::dispatch($makerequest);       
    }
}
