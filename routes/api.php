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
/**
Routes for Login. logout, refresh-token, register
*/
Route::post('login', 'LoginController@login')->name('login');
Route::get('logout', 'LogoutController@logout')->name('logout');
Route::post('refresh', 'LogoutController@refresh')->name('refresh');
Route::post('register','RegisterController@register')->name('register');

// First route that user visits on consumer app
Route::get('/user/github', 'github\githubController@makeRequest')->name('getGitToken');
// Route that user is forwarded back to after approving on server
Route::get('/oauth2/github','github\githubController@getRequest');
//github endpoints
Route::get('/github/user','github\githubController@getUserDetails');
Route::get('/github/user/update','github\githubController@getUserDetails');
Route::get('/github/user/repos','github\githubController@getRepoDetails');
Route::get('/github/user/repos/update','github\githubController@getReposOnly');
Route::post('/github/users/store','github\githubController@store');
Route::post('/github/users/update','github\githubController@update');
//get user's all github data stored in db
Route::get('/github/thisuser','github\githubController@showdata');
//developer and client
Route::get('/developer/clients','developer\admin\clientController@createClient')->name('create-client');