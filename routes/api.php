<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'],function() {
  Route::get('ping', 'PingController@index')->name('ping.index');

  Route::get('menus','MenuController@index')->name('menu.index');

  Route::post('login', 'LoginController@login')->name('login.login');
  Route::get('logout', 'LoginController@logout')->name('login.logout');
  Route::post('password/email', 'PasswordResetController@sendResetLinkEmail')->name('password.sendResetLinkEmail');
  Route::post('password/reset', 'PasswordResetController@resetPassword')->name('password.resetPassword');
  Route::post('register', 'RegisterController@create')->name('register.create');

  Route::apiResource('users','UserController');
  Route::apiResource('groups','GroupController');
  Route::apiResource('permissions','PermissionController');
  Route::apiResource('files','FileController');
  Route::get('files/{id}/preview','FileController@filePreview')->name('files.preview');
  Route::get('files/{id}/download','FileController@fileDownload')->name('files.download');
  Route::apiResource('file-groups','FileGroupController');

  Route::apiResource('tasks', 'TaskController');

  Route::get('timers', 'TimerController@index')->name('timers.index');
  Route::get('tasks/{id}/timers/{timer_id}', 'TimerController@show')->name('timers.show');
  Route::post('tasks/{id}/timers', 'TimerController@store')->name('timers.store');
  Route::put('tasks/{id}/timers/{timer_id}', 'TimerController@update')->name('timers.update');
  Route::delete('tasks/{id}/timers/{timer_id}', 'TimerController@delete')->name('timers.destroy');
  Route::post('tasks/{id}/timers/stop', 'TimerController@stop')->name('timers.stop');
  Route::get('tasks/timers/running', 'TimerController@running')->name('timers.running');

  Route::apiResource('notes', 'NoteController');

  Route::post('charts/byday', 'ChartController@byDay')->name('charts.byDay');
  Route::post('charts/bytag', 'ChartController@byTag')->name('charts.byTag');
  Route::post('charts/bytimer', 'ChartController@byTimer')->name('charts.byTimer');
});
