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



class UserController extends ApiController
{
    use HasApiTokens;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $client;
 
    public function __construct()
    {
        $this->middleware('auth:api')->except(['create','login']);
        $this->client= Client::find(6);
        //client will send id from front end. 
        //For now it's hard-coded
    }
      
    /**
     * Register a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
                        'name'=>'required',
                        'email'=>'required',
                        'password'=>'required|min:6|confirmed',
                ];
        $this->validate($request,$rules);

        $user = User::Create($request->all());
        $params=[
            'grant_type'=>'password',
            'client_id'=>$this->client->id,
            'client_secret'=>$this->client->secret,
            'username'=>request('email'),
            'password'=>request('password'),
            'scope'=>'*',
        ];

        $request->request->add($params);
        $makerequest=Request::create('oauth/token','POST');
        return Route::dispatch($makerequest);       
    }
    
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
