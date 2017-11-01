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


class LogoutController extends ApiController
{
	use HasApiTokens;
    private $client;
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $params=[
            'grant_type'=>'refresh_token',
            'client_id'=>request('client_id'),
            'client_secret'=>request('client_secret'),
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
