<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use Notifiable;
  use SoftDeletes;
  use HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'nickname', 'avatar'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token', 'activation_key', 'email_verified_at'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public static function encryptPassword($password){
    return bcrypt($password);
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function(User $user){
      if ($user->nickname == null) {
        $firstname = explode(" ", $user->name)[0];
        $user->nickname = strtolower($firstname)."-".$user->id;
        $user->save();
      }
    });

    static::creating(function(User $user) {
      if ($user->password) {
        $user->password = Hash::make($user->password);
      }
    });
  }

  public function accounts()
  {
    return $this->hasMany(SocialAccount::class);
  }

  public function scopeFindByEmailOrCreate($query, $providerUser)
  {
    return $query->firstOrCreate(['email' => $providerUser->getEmail()], [
      'email' => $providerUser->getEmail(),
      'name'  => $providerUser->getName(),
      'avatar' => $providerUser->getAvatar(),
      'nickname' => $providerUser->getNickname()
    ]);
  }

  public static function register(array $data)
  {
    return self::create($data);
  }

  public static function authenticate(array $data)
  {
    $user = self::where(['email' => $data['email']])->first();

    if($user && Hash::check($data['password'], $user->password)) {
      $token = $user->createToken('AppTokens')->accessToken;

      return compact('user', 'token');
    }

    return null;
  }

  public static function changePassword(array $data)
  {
    $user =  self::where('email', $data['email'])->first();

    $user->update(['password' => Hash::make($data['password'])]);

    return $user;
  }
}
