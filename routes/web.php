<?php
Auth::routes(['register' => false]);

Route::get('/','Front\\HomeController@index')->name('front.home');
Route::get('files/{id}/preview','Front\\FileController@filePreview')->name('front.file.preview');
Route::get('files/{id}/download','Front\\FileController@fileDownload')->name('front.file.download');
Route::get('files/{id}','Front\\FileController@show')->name('front.file.show');

// api
Route::get('/api/picture/day/{type}','Front\\PictureController@day')->name('front.picture.day');
Route::get('/api/menu','Front\\MenuController@index')->name('front.menu');

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
