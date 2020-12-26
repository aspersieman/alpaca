<?php
Auth::routes(['register' => false]);

Route::get('/','Front\\HomeController@index')->name('front.home');
Route::get('files/{id}/preview','Front\\FileController@filePreview')->name('front.file.preview');
Route::get('files/{id}/download','Front\\FileController@fileDownload')->name('front.file.download');
Route::get('files/{id}','Front\\FileController@show')->name('front.file.show');

// api
Route::get('/api/menu','Front\\MenuController@index')->name('front.menu');

Route::prefix('/api')->middleware(['auth'])->group(function()
{
  Route::get('/tasks/current', 'Front\\TaskController@current');
  Route::get('/tasks', 'Front\\TaskController@index');
  Route::post('/tasks', 'Front\\TaskController@store');
  Route::post('/tasks/order', 'Front\\TaskController@order');
  Route::delete('/tasks/{id}', 'Front\\TaskController@delete');
  Route::put('/tasks/{id}', 'Front\\TaskController@update');
  Route::post('/tasks/{id}/timers/stop', 'Front\\TimerController@stopRunning');
  Route::post('/tasks/{id}/timers', 'Front\\TimerController@store');
  Route::post('/tasks/{id}/status', 'Front\\TaskController@status');
  Route::post('/tasks/{id}/name', 'Front\\TaskController@name');
  Route::delete('/tasks/{id}/timer/{timer_id}', 'Front\\TimerController@delete');
  Route::put('/tasks/{id}/timer/{timer_id}', 'Front\\TimerController@update');
  Route::get('/tasks/timers/active', 'Front\\TimerController@running');
  Route::post('/chart/byday', 'Front\\ChartController@byDay');
  Route::post('/chart/bytag', 'Front\\ChartController@byTag');
  Route::post('/chart/bytimer', 'Front\\ChartController@byTimer');
  Route::post('/notes', 'Front\\NoteController@store');
  Route::delete('/notes/{id}', 'Front\\NoteController@delete');
  Route::put('/notes/{id}', 'Front\\NoteController@update');
});

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
Route::get(
  '/{vue_capture?}',
  'Front\\HomeController@index'
)->where('vue_capture', '^(?!storage).*$');
