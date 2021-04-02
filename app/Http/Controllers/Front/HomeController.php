<?php
namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
  public function index()
  {
    return view('welcome', [
      'auth_user' => Auth::user()
    ]);
  }
}
