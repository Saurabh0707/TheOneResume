<?php

namespace App\Http\Controllers\foreignApi;

use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\ApiController;
use GuzzleHttp\Promise;

class gitHubController extends ApiController
{
    use HasApiTokens;

    private $client;

    public function __construct()
    {
        //$this->middleware('checkOauthToken')->only(['getRepos', 'getAuthUser']);
        $this->middleware('auth:api');
    }
	public function makeRequest(Request $request)
    {
        
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


	public function getUserDetails(Request $request, $user)
    {
    		try
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	                $token = Cache::get('git_Oauth_token');
					$repo = new \GuzzleHttp\Client([
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
			    					]
					]);
					$promises = [
								    'user'   => $repo->getAsync('https://api.github.com/user'),
								    'userRepos' => $repo->getAsync('https://api.github.com/user/repos'),
								    'userOrgs'  => $repo->getAsync('https://api.github.com/users/'.$user.'/orgs'),
								];					
					$results 		= Promise\unwrap($promises);
					$results 		= Promise\settle($promises)->wait();
					$authUser 		= json_decode((string)$results['user']['value']->getBody(),true);
					$userRepos		= json_decode((string)$results['userRepos']['value']->getBody(),true);
					
					$userOrgs 		= json_decode((string)$results['userOrgs']['value']->getBody(),true);
					
			   		return response()->json(['data'=>
												['user'=>$authUser,
											  	 'userRepos'=>$userRepos,
												 'userOrgs'=>$userOrgs,						   		   				 	           		 
												 ]
			   								], '200');
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
    
    public function getRepoDetails(Request $request, $owner, $repo)
    {
    		try
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	                $token = Cache::get('git_Oauth_token');
	                $client = new \GuzzleHttp\Client([
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
			    					]
					]);
					$promises = [
								    'userRepo'=> $client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo),			    
								    'userRepoCommits'   => $client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/commits'),
								    'userRepoPullRequests' => $client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/pulls'),
								    'userRepoContributors'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/contributors'),
								    // 'userRepoCollaborators'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/collaborators'),//private
								    'userRepoLanguages'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/languages'),
								    'userRepoBranches'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/branches'),
								    'userRepoLabels'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/labels'),
								    'userRepoEvents'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/issues/events'),
								    'userRepoIssues'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/issues'),
								    'userRepoIssuesComments'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/issues/comments'),
								    'userRepoPullsComments'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/pulls/comments'),
								    'userRepoMilestones'=>$client->getAsync('https://api.github.com/repos/'.$owner.'/'.$repo.'/milestones'),
								];
					
					$results 				= Promise\unwrap($promises);
					$results 				= Promise\settle($promises)->wait();
					$userRepo				= json_decode((string)$results['userRepo']['value']->getBody(),true);
					$userRepoCommits		= json_decode((string)$results['userRepoCommits']['value']->getBody(),true);
					$userRepoPullRequests 			=json_decode((string)$results['userRepoPullRequests']['value']->getBody(),true);
					$userRepoContributors	=json_decode((string)$results['userRepoContributors']['value']->getBody(),true);
					// $userRepoCollaborators 	=json_decode((string)$results['userRepoCollaborators']['value']->getBody(),true);//private
					$userRepoLanguages 		=json_decode((string)$results['userRepoLanguages']['value']->getBody(),true);
					$userRepoBranches 		=json_decode((string)$results['userRepoBranches']['value']->getBody(),true);
					$userRepoLabels 		=json_decode((string)$results['userRepoLabels']['value']->getBody(),true);
					$userRepoEvents 		=json_decode((string)$results['userRepoEvents']['value']->getBody(),true);
					$userRepoIssues 		=json_decode((string)$results['userRepoIssues']['value']->getBody(),true);
					$userRepoIssuesComments =json_decode((string)$results['userRepoIssuesComments']['value']->getBody(),true);
					$userRepoPullsComments 	=json_decode((string)$results['userRepoPullsComments']['value']->getBody(),true);
					$userRepoMilestones 	=json_decode((string)$results['userRepoMilestones']['value']->getBody(),true);
					
					return response()->json(['data'=>
												[	
													'thisUserRepo' =>$userRepo,
													'thisUserRepoCommits' =>$userRepoCommits,
													'thisUserRepoPullRequests' =>$userRepoPullRequests,
													'thisUserRepoContributors' =>$userRepoContributors,
													// 'thisUserRepoCollaborators' =>'$userRepoCollaborators',
													'thisUserRepoLanguages' =>$userRepoLanguages,
													'thisUserRepoBranches' =>$userRepoBranches,
													'thisUserRepoLabels' =>$userRepoLabels,
													'thisUserRepoEvents ' =>$userRepoEvents ,
													'thisUserRepoIssues =' =>$userRepoIssues,
													'thisUserRepoIssuesComments' =>$userRepoIssuesComments,
													'thisUserRepoPullsComments ' =>$userRepoPullsComments, 
													'thisUserRepoMilestones' =>$userRepoMilestones,   		   				 	           		 
												]
			   								], '200');
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
    
    public function getReposOnly(Request $request)
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
			   		$data = json_decode((string) $resp->getBody(), true);
		    		return response()->json(['data'=>$data, 'code'=>'200'], '200');

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

    public function getAuthUserOnly(Request $request)
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
	    		$data = json_decode((string) $resp->getBody(), true);
		    	return response()->json(['data'=>$data, 'code'=>'200'], '200');

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

	
	 public function getUserOrgsOnly(Request $request)
		    {
		    	try 
		        {
		            if(Cache::has('git_Oauth_token'))
		            {
		        
		                $token = Cache::get('git_Oauth_token');
			    		$authUser = new \GuzzleHttp\Client;    	
			    		$resp = $authUser->get('https://api.github.com/users/'.$owner.'/orgs',	    	 
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
	 public function getOrgs(Request $request)
			    {
			    	try 
			        {
			            if(Cache::has('git_Oauth_token'))
			            {
			        
			                $token = Cache::get('git_Oauth_token');
				    		$authUser = new \GuzzleHttp\Client;    	
				    		$resp = $authUser->get('https://api.github.com/orgs/'.$orgs,	    	 
						    [
						    	'headers' =>[
									        	'Accept'     => 'application/json',
									        	'Content-Type'     => 'application/json',
									        	'Authorization'	=> 'Bearer '.$token,
				        					]
				    		]);
				    		$data = json_decode((string) $resp->getBody(), true);
		    				return response()->json(['data'=>$data, 'code'=>'200'], '200');

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
	    public function getOrgsProjects(Request $request)
	    {
	    	try 
	        {
	            if(Cache::has('git_Oauth_token'))
	            {
	        
	                $token = Cache::get('git_Oauth_token');
		    		$authUser = new \GuzzleHttp\Client;    	
		    		$resp = $authUser->get('https://api.github.com/orgs/'.$orgs.'/projects',	    	 
				    [
				    	'headers' =>[
							        	'Accept'     => 'application/json',
							        	'Content-Type'     => 'application/json',
							        	'Authorization'	=> 'Bearer '.$token,
		        					]
		    		]);
		    		$data = json_decode((string) $resp->getBody(), true);
		    		return response()->json(['data'=>$data, 'code'=>'200'], '200');
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
    
    // public function destroyCache()
    // {
    //     Cache::forget('git_Oauth_token');
    //     Cache::flush();
    //     return response()->json(['message'=>'Success', 'code'=>'200'], '200');	
    // }
}
