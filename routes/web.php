<?php
Auth::routes(['register' => false]);

Route::get('/','Front\\HomeController@index')->name('front.home');
Route::get('files/{id}/preview','Front\\FileController@filePreview')->name('front.file.preview');
Route::get('files/{id}/download','Front\\FileController@fileDownload')->name('front.file.download');
Route::get('files/{id}','Front\\FileController@show')->name('front.file.show');

// api
Route::get('/api/menu','Front\\MenuController@index')->name('front.menu');
Route::get('/api/tasks', 'Front\\TaskController@index');
Route::post('/api/tasks', 'Front\\TaskController@store');
Route::delete('/api/tasks/{id}', 'Front\\TaskController@delete');
Route::post('/api/tasks/{id}/timers/stop', 'Front\\TimerController@stopRunning');
Route::post('/api/tasks/{id}/timers', 'Front\\TimerController@store');
Route::post('/api/tasks/{id}/status', 'Front\\TaskController@status');
Route::post('/api/tasks/{id}/name', 'Front\\TaskController@name');
Route::delete('/api/tasks/{id}/timer/{timer_id}', 'Front\\TimerController@delete');
Route::get('/api/task/timers/active', 'Front\\TimerController@running');

// admin
Route::prefix('/api/admin')->namespace('Admin')->middleware(['auth'])->group(function()
{
  // resource routes
  Route::resource('users','UserController');
  Route::resource('groups','GroupController');
  Route::resource('permissions','PermissionController');
  Route::resource('files','FileController');
  Route::resource('file-groups','FileGroupController');
  Route::resource('pictures','PictureController');
});

// Match all other routes to the SPA
Route::get('/{vue_capture?}', function () {
  return view('welcome', [
    'auth_user' => Auth::user()
  ]);
})->where('vue_capture', '^(?!storage).*$'); 
