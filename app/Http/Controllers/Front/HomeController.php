<?php
namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Auth;

class HomeController extends FrontController
{
  public function index()
  {
    return view('welcome', [
      'auth_user' => Auth::user()
    ]);
  }
}
