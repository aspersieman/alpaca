<?php
// Match all non-api routes to the SPA
Route::get(
  '{all}',
  'Front\\HomeController@index')
  ->where('all', '^(?!api).*$')
  ->name('home.index');
