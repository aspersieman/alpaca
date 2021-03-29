<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'],function() {
  Route::get('ping', 'PingController@index');

  Route::post('login', 'LoginController@login');
  Route::get('logout', 'LoginController@logout');
  Route::post('password/email', 'PasswordResetController@sendResetLinkEmail');
  Route::post('password/reset', 'PasswordResetController@resetPassword');

  Route::put('users/{user}', 'UserController@update');
  Route::get('users/main', 'UserController@main');

  Route::apiResource('task', 'TaskController');

  Route::post('/task/{id}/timer', 'TimerController@store');
  Route::put('/task/{id}/timer/{timer_id}', 'TimerController@update');
  Route::delete('/task/{id}/timer/{timer_id}', 'TimerController@delete');
  Route::post('/task/{id}/timer/stop', 'TimerController@stop');
  Route::get('/task/timer/running', 'TimerController@running');

  Route::post('/chart/byday', 'ChartController@byDay');
  Route::post('/chart/bytag', 'ChartController@byTag');
  Route::post('/chart/bytimer', 'ChartController@byTimer');
});
