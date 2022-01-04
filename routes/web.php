<?php
Route::get( '/admin/backups/google/drive-callback',  [ App\Http\Controllers\Admin\BackupController::class, 'fetchVerificateCode' ] );
Route::get('files/{id}/download','\App\Http\Controllers\Api\V1\FileController@fileDownload')->name('files.download');
Route::get('files/{id}/preview','\App\Http\Controllers\Api\V1\FileController@filePreview')->name('files.preview');
Route::get('avatar/{id}','\App\Http\Controllers\Api\V1\UserController@showAvatar')->name('users.showAvatar');

// Match all non-api routes to the SPA
Route::get(
  '{all}',
  'Front\\HomeController@index')
  ->where('all', '^(?!api).*$')
  ->name('home.index');
