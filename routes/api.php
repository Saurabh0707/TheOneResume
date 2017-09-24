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
Route::get('/github/user','foreignApi\githubController@getUserDetails');
Route::get('/github/user/update','foreignApi\githubController@getUserDetails');

Route::get('/github/user/repos','foreignApi\githubController@getRepoDetails');//redundant
Route::get('/github/user/repos/update','foreignApi\githubController@getReposOnly');

Route::post('/github/users/store','foreignApi\githubController@store');
Route::post('/github/users/update','foreignApi\githubController@update');

//get user's all github data stored in db
Route::get('/github/thisuser','foreignApi\githubController@showdata');

//developer and client
Route::get('/developer/clients','developer\admin\clientController@createClient')->name('create-client');

