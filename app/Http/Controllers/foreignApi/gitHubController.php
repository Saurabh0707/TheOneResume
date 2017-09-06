<?php

namespace App\Http\Controllers\foreignApi;

use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\ApiController;

class gitHubController extends ApiController
{
    use HasApiTokens;

    private $client;

    public function __construct()
    {
        //$this->middleware('checkOauthToken')->only(['getRepos', 'getAuthUser']);
        //$this->middleware('auth:api');
    }
	public function makeRequest(Request $request)
    {
        
    	 // Build the query parameter string to pass auth information to our request
	   	$query = http_build_query([
	        'client_id' => 'c98f06e52785cdf675ec',
	        'redirect_uri' => 'http://localhost:8000/api/oauth2/github',
	        'response_type' => 'code',
	        'scope' => '*'
	    ]);
	    return redirect('https://github.com/login/oauth/authorize?' . $query);
    }

    public function getRequest(Request $request)
    {
        
    	$http = new \GuzzleHttp\Client;

	    $response = $http->post('https://github.com/login/oauth/access_token', 
	    	[
		        'form_params' => [
						            'grant_type' => 'authorization_code',
						            'client_id' => 'c98f06e52785cdf675ec',
						            'client_secret' => 'e93b809b53a6fed3289b780251220b1ef563ea6a', // from admin panel above
						            'redirect_uri' => 'http://localhost:8000/api/oauth2/github',
						            'code' => $request->code // Get code from the callback
		        				]
		    ], 
		    [
		    	'headers' =>[
					        	'Accept'     => 'application/json',
					        	'Content-Type'     => 'application/json',
        					]
    		]);
	    $body=$response->getBody();
	    return $this->storeAccessTokenInCache(substr($body,13,40));	
	 	}

    
    public function getRepos(Request $request)
    {
    		try
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	                $token = Cache::get('git_Oauth_token');
					$repo = new \GuzzleHttp\Client;    	
					$resp = $repo->get('https://api.github.com/user/repos',	    	 
				    [
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
			    					]
					]);
			   		return $resp->getbody();  
	            }   
	            else
	            {
	                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');
	            }
	        } 
	        catch (ClientException $e) 
	        {
	            if($e->getResponse()->getStatusCode()==401)
	            {
	                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');	            
	            }
	        }    		
    }

    public function getAuthUser(Request $request)
    {
    	try 
        {
            if(Cache::has('git_Oauth_token'))
            {
        
                $token = Cache::get('git_Oauth_token');
	    		$authUser = new \GuzzleHttp\Client;    	
	    		$resp = $authUser->get('https://api.github.com/user',	    	 
			    [
			    	'headers' =>[
						        	'Accept'     => 'application/json',
						        	'Content-Type'     => 'application/json',
						        	'Authorization'	=> 'Bearer '.$token,
	        					]
	    		]);
	    		return $resp->getbody();

            }   
            else
            {
                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');       

            }
        } 
        catch (ClientException $e) 
        {
            //return redirect()->route('getGitToken');
            if($e->getResponse()->getStatusCode()==401)
            {
               return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');
	            
            }

        }
    }

	 public function getRepoCommits(Request $request, $owner, $repo)
	    {
	    	try 
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	        
	                $token = Cache::get('git_Oauth_token');
		    		$authUser = new \GuzzleHttp\Client;    	
		    		$resp = $authUser->get('https://api.github.com/repos/'.$owner.'/'.$repo.'/commits',	    	 
				    [
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
		        					]
		    		]);
		    		return $resp->getbody();

	            }   
	            else
	            {
	               return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');
		            

	            }
	        } 
	        catch (ClientException $e) 
	        {
	            //return redirect()->route('getGitToken');
	            if($e->getResponse()->getStatusCode()==401)
	            {
	                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');
		            
	            }

	        }
	    }

	 public function getRepoPulls(Request $request)
	    {
	    	try 
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	        
	                $token = Cache::get('git_Oauth_token');
		    		$authUser = new \GuzzleHttp\Client;    	
		    		$resp = $authUser->get('https://api.github.com/repos/'.$owner.'/'.$repo.'/pulls',	    	 
				    [
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
		        					]
		    		]);
		    		return $resp->getbody();

	            }   
	            else
	            {
	                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');
		            

	            }
	        } 
	        catch (ClientException $e) 
	        {
	            if($e->getResponse()->getStatusCode()==401)
	            {
	                return response()->json(['error'=>'Unauthorised To Use GitHub Endpoints', 'code'=>'401'], '401');		            
	            }

	        }
	    }

    public function storeAccessTokenInCache($data)
    {	
    	Cache::forever('git_Oauth_token',$data);
    	return response()->json(['message'=>'Success', 'code'=>'200', 'access_token'=>Cache::get('git_Oauth_token')], '200');	
    }
    // needs to called during logout
    public function destroyCache()
    {
        Cache::forget('git_Oauth_token');
        Cache::flush();
        return response()->json(['message'=>'Success', 'code'=>'200'], '200');	
    }
}
