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
Routes for Login. logout, refresh-token
*/
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'User\UserController@login');
Route::get('logout', 'User\UserController@logout')->name('logout');
Route::post('refresh', 'User\UserController@refresh');
/**
Routes for Register
*/
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'User\UserController@store');