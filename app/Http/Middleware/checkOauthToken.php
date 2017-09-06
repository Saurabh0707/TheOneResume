<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class checkOauthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        try 
        {
            if(Cache::has('git_Oauth_token'))
            {
                //make request
                return $next($request);
            }   
            else
            {
                Cache::forget('git_Oauth_token');
                Cache::flush();
                return redirect()->route('getGitToken'); 
            }
        } 
        catch (ClientException $e) 
        {
            //return redirect()->route('getGitToken');
            if($e->getResponse()->getStatusCode()==401)
            {
                //return $this->errorResponse('Unauthorised',401); this cannot be done because hamari ap client hai git ki naa ki user hamara
                Cache::forget('git_Oauth_token');
                Cache::flush();
                return redirect()->route('getGitToken'); 
            }

        }
       
    }
}
