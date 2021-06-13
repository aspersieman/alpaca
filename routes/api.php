<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'],function() {
  Route::get('ping', 'PingController@index')->name('ping.index');

  Route::get('menus','MenuController@index')->name('menu.index');

  Route::post('login', 'LoginController@login')->name('login.login');
  Route::get('logout', 'LoginController@logout')->name('login.logout');
  Route::post('password/email', 'PasswordResetController@sendResetLinkEmail')->name('passwordReset.sendResetLinkEmail');
  Route::post('password/reset', 'PasswordResetController@resetPassword')->name('passwordReset.resetPassword');

  Route::apiResource('users','UserController');
  Route::apiResource('groups','GroupController');
  Route::apiResource('permissions','PermissionController');
  Route::apiResource('files','FileController');
  Route::get('files/{id}/preview','FileController@filePreview')->name('file.preview');
  Route::get('files/{id}/download','FileController@fileDownload')->name('file.download');
  Route::apiResource('file-groups','FileGroupController');

  Route::apiResource('tasks', 'TaskController');

  Route::post('tasks/{id}/timers', 'TimerController@store')->name('timer.store');
  Route::put('tasks/{id}/timers/{timer_id}', 'TimerController@update')->name('timer.update');
  Route::delete('tasks/{id}/timers/{timer_id}', 'TimerController@delete')->name('timer.destroy');
  Route::post('tasks/{id}/timers/stop', 'TimerController@stop')->name('timer.stop');
  Route::get('tasks/timers/running', 'TimerController@running')->name('timer.running');

  Route::apiResource('notes', 'NoteController');

  Route::post('charts/byday', 'ChartController@byDay')->name('chart.byDay');
  Route::post('charts/bytag', 'ChartController@byTag')->name('chart.byTag');
  Route::post('charts/bytimer', 'ChartController@byTimer')->name('chart.byTimer');
});
