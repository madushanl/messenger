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

Route::post('test', function() {
    dd('sdsd');
});

Route::post('users', 'UserController@store')->name('users.store');
Route::post('login', 'UserController@authenticate')->name('login');

Route::group([ 'middleware' => [ 'auth' ] ], function() {
	Route::post('logout', 'UserController@logout')->name('logout');
	Route::get('chats', 'ChatController@index')->name('user.chat_list');
	Route::post('chats', 'ChatController@store')->name('chat.create');
	Route::post('chats/get-conversation-by-users', 'ChatController@getConversation')->name('chat.get_by_users');
	Route::get('chats/{id}/messages', 'MessageController@getChatMessages')->name('chat.messages');
	Route::get('chats/{id}', 'ChatController@show')->name('chat.by_id');
	Route::post('chats/{id}/messages', 'MessageController@store')->name('chat.messages.save');
	Route::get('users', 'UserController@search')->name('user.search');
});
