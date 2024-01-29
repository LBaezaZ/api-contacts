<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::post('api/user/signup', 'UserController@signUp');
Route::post('api/user/signin', 'UserController@signIn');
Route::put('api/user/update', 'UserController@update')->middleware('api.auth');
Route::get('api/user/detail/{id}', 'UserController@detail')->middleware('api.auth');

Route::post('api/contact/addContact', 'ContactsController@addContact')->middleware('api.auth');
Route::put('api/contact/update/{id}', 'ContactsController@update')->middleware('api.auth');
Route::get('api/contact/detail/{id}', 'ContactsController@detail')->middleware('api.auth');
Route::get('api/contact/list', 'ContactsController@list')->middleware('api.auth');
Route::delete('api/contact/delete/{id}', 'ContactsController@delete')->middleware('api.auth');

Route::post('api/phone/addPhone', 'PhonesController@addPhone');
Route::put('api/phone/update/{id}', 'PhonesController@update');
Route::get('api/phone/detail/{id}', 'PhonesController@detail');
Route::get('api/phone/list/{id}', 'PhonesController@list');
Route::delete('api/phone/delete/{id}', 'PhonesController@delete');