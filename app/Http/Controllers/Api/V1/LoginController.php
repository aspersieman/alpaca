<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
  use AuthenticatesUsers;

  public function __construct()
  {
    //$this->middleware('auth:api')->only('logout');
  }

  public function login(Request $request)
  {

    // If the class is using the ThrottlesLogins trait, we can automatically throttle
    // the login attempts for this application. We'll key this by the username and
    // the IP address of the client making these requests into this application.
    if ($this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);

      return $this->sendLockoutResponse($request);
    }

    $userDetailsWithToken = User::authenticate($request->only('email', 'password'));
    if($userDetailsWithToken) {
      return response($userDetailsWithToken);
    } else {
      // If the login attempt was unsuccessful we will increment the number of attempts
      // to login and redirect the user back to the login form. Of course, when this
      // user surpasses their maximum number of attempts they will get locked out.
      $this->incrementLoginAttempts($request);
    }

    return response(['error' =>'Email or password incorrect'], 422);
  }

  public function logout()
  {
    if (auth()->user()) {
      $token = auth()->user()->token();
      $token->revoke();

      return $this->sendResponseOk("Logged out.");
    } else {
      return $this->sendResponseNoContent();
    }
  }
}
