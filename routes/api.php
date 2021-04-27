<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'],function() {
  Route::get('ping', 'PingController@index');

  Route::get('menus','MenuController@index')->name('front.menu');

  Route::post('login', 'LoginController@login');
  Route::get('logout', 'LoginController@logout');
  Route::post('password/email', 'PasswordResetController@sendResetLinkEmail');
  Route::post('password/reset', 'PasswordResetController@resetPassword');

  Route::apiResource('users','UserController');
  Route::apiResource('groups','GroupController');
  Route::apiResource('permissions','PermissionController');
  Route::apiResource('files','FileController');
  Route::get('files/{id}/preview','FileController@filePreview')->name('front.file.preview');
  Route::get('files/{id}/download','FileController@fileDownload')->name('front.file.download');
  Route::apiResource('file-groups','FileGroupController');

  Route::apiResource('tasks', 'TaskController');

  Route::post('tasks/{id}/timers', 'TimerController@store');
  Route::put('tasks/{id}/timers/{timer_id}', 'TimerController@update');
  Route::delete('tasks/{id}/timers/{timer_id}', 'TimerController@delete');
  Route::post('tasks/{id}/timers/stop', 'TimerController@stop');
  Route::get('tasks/timers/running', 'TimerController@running');

  Route::apiResource('notes', 'NoteController');

  Route::post('charts/byday', 'ChartController@byDay');
  Route::post('charts/bytag', 'ChartController@byTag');
  Route::post('charts/bytimer', 'ChartController@byTimer');
});
