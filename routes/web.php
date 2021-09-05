<?php
Route::get( '/admin/backups/google/',  [ App\Http\Controllers\Admin\BackupController::class, 'index' ] );
Route::get( '/admin/backups/google/drive-callback',  [ App\Http\Controllers\Admin\BackupController::class, 'fetchVerificateCode' ] );
Route::get( '/admin/backups/google/upload-file',  [ App\Http\Controllers\Admin\BackupController::class, 'sendToDrive' ] );

// Match all non-api routes to the SPA
Route::get(
  '{all}',
  'Front\\HomeController@index')
  ->where('all', '^(?!api).*$')
  ->name('home.index');
