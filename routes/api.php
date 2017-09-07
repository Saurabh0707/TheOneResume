<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('users', 'User\UserController', ['only'=>['store']]);

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('web');//if declared in web.php then not working
Route::post('login', 'User\UserController@login');
Route::get('logout', 'User\UserController@logout')->name('logout');

Route::post('refresh', 'User\UserController@refresh');


Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register')->middleware('web');;
Route::post('register', 'User\UserController@store');

// First route that user visits on consumer app
Route::get('/user/github', 'foreignApi\githubController@makeRequest')->name('getGitToken');

// Route that user is forwarded back to after approving on server
Route::get('/oauth2/github','foreignApi\githubController@getRequest');

//github endpoints
Route::get('/user/repos','foreignApi\githubController@getRepos');
Route::get('/user','foreignApi\githubController@getAuthUser');
Route::get('/repos/{owner}/{repo}/commits','foreignApi\githubController@getRepoCommits');
Route::get('/repos/{owner}/{repo}/pulls','foreignApi\githubController@getRepoPulls');
Route::get('/users/{owner}/orgs','foreignApi\githubController@getUserOrgs');
Route::get('/orgs/{orgs}','foreignApi\githubController@getOrgs');
Route::get('/orgs/{orgs}/projects','foreignApi\githubController@getOrgsProjects');


Route::get('/createCache','foreignApi\githubController@storeAccessTokenInCache');
Route::get('/clearCache','foreignApi\githubController@destroyCache');

//developer and client
Route::get('/developer/clients','developer\admin\clientController@createClient')->name('create-client');
