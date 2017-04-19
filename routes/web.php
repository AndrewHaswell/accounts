<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
  return view('welcome');
});

Route::auth(); //Removed to prevent registration

// Authentication Routes...
//Route::get('login', 'Auth\AuthController@showLoginForm');
//Route::post('login', 'Auth\AuthController@login');
//Route::get('logout', 'Auth\AuthController@logout');

// Password Reset Routes...
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\PasswordController@reset');

Route::get('/home', 'HomeController@index');
Route::get('/accounts/{id}', 'AccountsController@detail');
Route::get('/future/{id}/{month}', 'AccountsController@future');
Route::get('/transactions/{id}', 'TransactionsController@detail');
Route::get('/transactions', 'TransactionsController@index');
Route::get('/accounts', 'AccountsController@index');
Route::get('/balance', 'TestController@balance');
Route::get('/test', 'TestController@test');
Route::resource('transactions', 'TransactionsController');
Route::resource('payments', 'PaymentsController');
Route::resource('schedules', 'SchedulesController');