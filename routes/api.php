<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1'],function() {
  Route::get('ping', 'PingController@index');

  Route::post('login', 'LoginController@login');
  Route::get('logout', 'LoginController@logout');
  Route::post('password/email', 'PasswordResetController@sendResetLinkEmail');
  Route::post('password/reset', 'PasswordResetController@resetPassword');

  Route::put('users/{user}', 'UsersController@update');
  Route::get('users/main', 'UsersController@main');

  Route::resource('tasks', 'TaskController');

});
