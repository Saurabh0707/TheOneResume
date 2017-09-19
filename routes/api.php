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
Route::post('login', 'User\UserController@login');
Route::get('logout', 'User\UserController@logout')->name('logout');
Route::post('refresh', 'User\UserController@refresh');
Route::post('register', 'User\UserController@store');

// First route that user visits on consumer app
Route::get('/user/github', 'foreignApi\githubController@makeRequest')->name('getGitToken');

// Route that user is forwarded back to after approving on server
Route::get('/oauth2/github','foreignApi\githubController@getRequest');

//github endpoints
//userRepo
Route::get('/github/user','foreignApi\githubController@getAuthUserOnly');
Route::get('/github/user/update','foreignApi\githubController@getAuthUserOnly');

Route::get('/github/user/{user}','foreignApi\githubController@getUserDetails');//redundant
Route::get('/github/user/{user}/update','foreignApi\githubController@getUserDetails');

Route::get('/github/user/repos','foreignApi\githubController@getReposOnly');//redundant
Route::get('/github/user/repos/update','foreignApi\githubController@getReposOnly');

Route::get('/github/user/repos/{owner}/{repo}','foreignApi\githubController@getRepoDetails');
Route::get('/github/user/repos/{owner}/{repo}/update','foreignApi\githubController@getRepoDetails');

Route::post('/github/users/store','foreignApi\githubController@store');
Route::post('/github/users/update','foreignApi\githubController@update');

//github and api both return null returns null even if orgs are private
Route::get('/github/users/{username}/orgs','foreignApi\githubController@getUserOrgsOnly');
//"githubmessage": "You need at least read:org scope or user scope to list your organizations.",
//api is not entering getAuthUserOrgsOnly
//Route::get('/github/user/orgs','foreignApi\githubController@getAuthUserOrgsOnly');

//organisation information
Route::get('/github/orgs/{orgs}','foreignApi\githubController@getOrgs');
//not working
//"githubmessage": "If you would like to help us test the Projects API during its preview period, you must specify a custom media type in the 'Accept' header. Please see the docs for full details.",
//Route::get('/github/orgs/{orgs}/projects','foreignApi\githubController@getOrgsProjects');


//get user's all github data
Route::get('/github/thisuser','foreignApi\githubController@showdata');

//developer and client
Route::get('/developer/clients','developer\admin\clientController@createClient')->name('create-client');

