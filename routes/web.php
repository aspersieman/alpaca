<?php
Route::get( '/admin/backups/google/drive-callback',  [ App\Http\Controllers\Admin\BackupController::class, 'fetchVerificateCode' ] );

// Match all non-api routes to the SPA
Route::get(
  '{all}',
  'Front\\HomeController@index')
  ->where('all', '^(?!api).*$')
  ->name('home.index');
