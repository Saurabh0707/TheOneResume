<?php

namespace App\Http\Controllers\foreignApi;

use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\ApiController;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Githubrepo;
use App\Githubuser;
use App\Repobranche;
use App\Repocommit;
use App\Repocontributor;
use App\Repolang;

class gitHubController extends ApiController
{
    use HasApiTokens;

    private $client;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['makeRequest', 'getRequest']);
    }

	 /* Display a user details.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
    public function showdata()
    {	        
        $x = Auth::user('api')->id;
        $data = User::with('githubusers.githubrepos.repobranches','githubusers.githubrepos.repocommits','githubusers.githubrepos.repocontributors','githubusers.githubrepos.repolangs','githubusers.githubrepos.repoPRs')->find($x);
        return response()->json(['data' => ['userRepoData'=>$data], 'code'=> 200],200);
    }
	
	public function store(Request $request)
	{
		$inputs = $request->all();	    
		$x = Auth::user('api')->id;
		$user= User::find($x);		
		$existingUser  =  $user->githubusers()->where('username', $inputs['data']['userRepoData']['githubusers']['username'])->first();
		if(!$existingUser)
	    {
	    	 return response()->json(['message'=>'User Already Exists','code'=>406],406);
	    }
	    $this->insertIntoTables($user, $inputs);
		return response()->json(['message' => 'Records Saved', 'code'=> 200],200);				          
	}

	public function update(Request $request)
	{
		$inputs = $request->all();	    
		$x = Auth::user('api')->id;
		$user= User::find($x);
		
		$existingUser  =  $user->githubusers()->where('username', $inputs['data']['userRepoData']['githubusers']['username'])->first();

		if($existingUser)
	    {
	    	 return response()->json(['message'=>'User Not Found', 'code'=>404],404);
	    }
	    else
	    {
	    	$user->githubusers()->delete();	
	    }
	    $this->insertIntoTables($user, $inputs);
	    return response()->json(['message' => 'Records Updated', 'code'=> 200],200);				          

	}
	 /* Get Token for a user from GithubApps.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 * client_id is the ID given by github to the API
	 */
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
	/* Get Response after approval by the server.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * 
	 * client_id is the ID given by github to the API
	 * client_secret is the secret given by github to the api
	 */
    public function getRequest(Request $request)
    {
        
    	$http = new \GuzzleHttp\Client;

	    $response = $http->post('https://github.com/login/oauth/access_token', 
	    	[
		        'form_params' => [
						            'grant_type' => 'authorization_code',
						            'client_id' => 'c98f06e52785cdf675ec',
						            'client_secret' => 'e93b809b53a6fed3289b780251220b1ef563ea6a',
						            'redirect_uri' => 'http://localhost:8000/api/oauth2/github',
						            'code' => $request->code
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
	 /* Get Public User details without being authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 *
	 */
	public function getUserDetails(Request $request)
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
					$promise = $repo->get('https://api.github.com/user');
					$results = json_decode((string)$promise->getBody(),false);
					$user = $results->login;
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
	/* Get Authenticated User's Repository's all details at once.
	*
	* @param  \Illuminate\Http\Request  $request
	*
	*/
    public function getRepoDetails(Request $request)
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
					
					$promise = $client->get('https://api.github.com/user/repos');
					// $results = json_decode((string)$promise->getBody(),false);
					$results = json_decode($promise->getBody()->getContents(),true);
					$array	 = array();	
					$new_array	 = array();	
					for ($i	=0; $i < sizeof($results); $i++) 
					{
						$fullname = $results[$i]['full_name'];
						$promises = [
								    'userRepo'=> $client->getAsync('https://api.github.com/repos/'.$fullname),			    
								    'userRepoCommits'   => $client->getAsync('https://api.github.com/repos/'.$fullname.'/commits'),
								    'userRepoPullRequests' => $client->getAsync('https://api.github.com/repos/'.$fullname.'/pulls'),
								    'userRepoContributors'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/contributors'),
								    'userRepoLanguages'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/languages'),
								    'userRepoBranches'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/branches'),
								    'userRepoLabels'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/labels'),
								    'userRepoEvents'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/issues/events'),
								    'userRepoIssues'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/issues'),
								    'userRepoIssuesComments'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/issues/comments'),
								    'userRepoPullsComments'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/pulls/comments'),
								    'userRepoMilestones'=>$client->getAsync('https://api.github.com/repos/'.$fullname.'/milestones'),
								];
					
						$results2 				= Promise\unwrap($promises);
						$results2 				= Promise\settle($promises)->wait();
						$userRepo				= json_decode((string)$results2['userRepo']['value']->getBody(),true);
						$userRepoCommits		= json_decode((string)$results2['userRepoCommits']['value']->getBody(),true);
						$userRepoPullRequests 	= json_decode((string)$results2['userRepoPullRequests']['value']->getBody(),true);
						$userRepoContributors	= json_decode((string)$results2['userRepoContributors']['value']->getBody(),true);
						$userRepoLanguages 		= json_decode((string)$results2['userRepoLanguages']['value']->getBody(),true);
						$userRepoBranches 		= json_decode((string)$results2['userRepoBranches']['value']->getBody(),true);
						$userRepoLabels 		= json_decode((string)$results2['userRepoLabels']['value']->getBody(),true);
						$userRepoEvents 		= json_decode((string)$results2['userRepoEvents']['value']->getBody(),true);
						$userRepoIssues 		= json_decode((string)$results2['userRepoIssues']['value']->getBody(),true);
						$userRepoIssuesComments = json_decode((string)$results2['userRepoIssuesComments']['value']->getBody(),true);
						$userRepoPullsComments 	= json_decode((string)$results2['userRepoPullsComments']['value']->getBody(),true);
						$userRepoMilestones 	= json_decode((string)$results2['userRepoMilestones']['value']->getBody(),true);
						$results[$i]=array();
						array_push($results[$i],[	
													'thisUserRepo' =>$userRepo,
													'thisUserRepoCommits' =>$userRepoCommits,
													'thisUserRepoPullRequests' =>$userRepoPullRequests,
													'thisUserRepoContributors' =>$userRepoContributors,
													'thisUserRepoLanguages' =>$userRepoLanguages,
													'thisUserRepoBranches' =>$userRepoBranches,
													'thisUserRepoLabels' =>$userRepoLabels,
													'thisUserRepoEvents ' =>$userRepoEvents ,
													'thisUserRepoIssues =' =>$userRepoIssues,
													'thisUserRepoIssuesComments' =>$userRepoIssuesComments,
													'thisUserRepoPullsComments ' =>$userRepoPullsComments, 
													'thisUserRepoMilestones' =>$userRepoMilestones,	 
												]);	
							array_push($array, $results[$i]);							
					}
					return response()->json(['data'=> $array],'200');
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
    private function insertIntoTables($inputs, $user)
	{
		foreach($inputs['data']['userRepoData']['githubusers'] as $githubuser)
		{
            $insertGithubUser =[
            		'username'			=>		$githubuser["username"],
            		'html_url'			=>		$githubuser["html_url"],
            		'name'				=>		$githubuser["name"],
            		'company'			=>		$githubuser["company"],
            		'location'			=>		$githubuser["location"],
            		'user_created_at'	=>		$githubuser["user_created_at"],
            		'user_updated_at'	=>		$githubuser["user_updated_at"],
            		'public_repos'		=>		$githubuser["public_repos"],
            		'public_gists'		=>		$githubuser["public_gists"],
    		];

    		$insertedGithubUser = $user->githubusers()->firstOrCreate($insertGithubUser);
    		foreach($githubuser['githubrepos'] as $githubrepo)
			{
				 $insertGithubRepo =[
            		'owner'					=>		$githubrepo["owner"],
            		'name'					=>		$githubrepo["name"],
            		'html_url'				=>		$githubrepo["html_url"],
            		'clone_url'				=>		$githubrepo["clone_url"],
            		'repo_created_at'		=>		$githubrepo["repo_created_at"],
            		'repo_updated_at'		=>		$githubrepo["repo_updated_at"],
            		'repo_pushed_at'		=>		$githubrepo["repo_pushed_at"],
            		'public_repos'			=>		$githubrepo["public_repos"],
            		'no_of_commits'			=>		$githubrepo["no_of_commits"],
            		'no_of_branches'		=>		$githubrepo["no_of_branches"],
            		'no_of_pullrequests'	=>		$githubrepo["no_of_pullrequests"],
            		'no_of_contributors'	=>		$githubrepo["no_of_contributors"],
    			];
				$insertedGithubRepo = $insertedGithubUser->githubrepos()->firstOrCreate($insertGithubRepo);
				foreach($githubrepo['repocommits'] as $repocommit)
				{
					$insertGithubRepoCommits =
					[
	            		'sha'						=>      $repocommit["sha"],
	            		'author'					=>		$repocommit["author"],
	            		'committer'					=>		$repocommit["committer"],
	            		'message'				=>		$repocommit["message"],
	            		'commit_created_at'				=>		$repocommit["commit_created_at"],
	            		'commit_updated_at'		=>		$repocommit["commit_updated_at"],
    				];
    			 	$insertedGithubRepoCommit = $insertedGithubRepo->repocommits()->firstOrCreate($insertGithubRepoCommits);
				}
				foreach($githubrepo['repocontributors'] as $repocontributor)
				{
					$insertGithubRepoContributors =
					[
	            		'name'					=>		$repocontributor["name"],
	            		'contributions'					=>		$repocontributor["contributions"],
	           		];	
	           		$insertedGithubRepoContributor = $insertedGithubRepo->repocontributors()->firstOrCreate($insertGithubRepoContributors);
				}
				foreach($githubrepo['repobranches'] as $repobranch)
				{
					$insertGithubRepoBranches =
					[
	            		'name'					=>		$repobranch["name"],
	     			];	
	     			$insertedGithubRepoBranch = $insertedGithubRepo->repobranches()->firstOrCreate($insertGithubRepoBranches);
				}
				foreach($githubrepo['repolangs'] as $repolang)
				{
					$insertGithubRepoLangs =
					[
	            		'name'					=>		$repolang["name"],
	            		'lines'					=>		$repolang["lines"],
	     			];	
	     			$insertedGithubRepoLang = $insertedGithubRepo->repolangs()->firstOrCreate($insertGithubRepoLangs);	   
	     		}
	     		foreach($githubrepo['repoPRs'] as $repoPR)
				{
					$insertGithubRepoPRs =
					[
	            		'name'					=>		$repoPR["name"],
	            		"state"					=>		$repoPR["state"],
	            		"title"					=>		$repoPR["title"],
	            		"body"					=>		$repoPR["body"],
	            		"assignee"				=>		$repoPR["assignee"],
	            		"creator"				=>		$repoPR["creator"], 
	            		"open_issues"			=>		$repoPR["open_issues"],
	            		"closed_issues"			=>		$repoPR["closed_issues"],
	            		"created_at"			=>		$repoPR["created_at"], 
	            		"updated_at"			=>		$repoPR["updated_at"],
	            		"closed_at"				=>		$repoPR["closed_at"],
	            		"merged_at"				=>		$repoPR["merged_at"],
	     			];	
	     			$insertedGithubRepoPR = $insertedGithubRepo->repoPRs()->firstOrCreate($insertGithubRepoPRs);	   
	     		}
			}
        }
	}
	/* Store access token in Cache.
	*
	* @param  $data
	*
	*/
    public function storeAccessTokenInCache($data)
    {	
    	Cache::forever('git_Oauth_token',$data);
    	return response()->json(['message'=>'Success', 'code'=>'200', 'access_token'=>Cache::get('git_Oauth_token')], '200');	
    }
}
