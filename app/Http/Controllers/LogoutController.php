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


class LogoutController extends ApiController
{
	use HasApiTokens;
    private $client;
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    //function to retrieve the client credentials
    public function getClient($client_id)
    {
        //dynamically retrieving the client_id and secret
        $this->client=Client::find($client_id);
    }
    /**
     * Refresh A token.
     *
     */
    public function refresh(Request $request)
    {
        $this->validate($request,[
            'refresh_token'=>'required',
        ]);
        $this->getClient(request('client_id'));        
        $params=[
            'grant_type'=>'refresh_token',
            'client_id'=>$this->client->id,
            'client_secret'=>$this->client->secret,
            'username'=>request('username'),
            'password'=>request('password'),
        ];
        $request->request->add($params);
        $proxy=Request::create('oauth/token','POST');
        return Route::dispatch($proxy);
    }
    /**
     * Logot a user.
     *
     */
    public function logout(Request $request)
    {
        Auth::user('api')->token()->revoke();
        Cache::flush();
        return response()->json(['message' => 'User was logged out', 'code'=> 200],200);
    }
}
