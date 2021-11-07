<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

use App\Traits\UserTrait;

use \DateTimeInterface;

class User extends Authenticatable
{
  use Notifiable,
    SoftDeletes,
    HasApiTokens,
    UserTrait;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'nickname', 'avatar', 'remember_token', 'permissions', 'last_login', 'active', 'activation_key'
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

  protected $with = [
    'groups'
  ];

  /**
   * Prepare a date for array / JSON serialization.
   *
   * @param  \DateTimeInterface  $date
   * @return string
   */
  protected function serializeDate(DateTimeInterface $date)
  {
      return $date->format('Y-m-d H:i:s');
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
