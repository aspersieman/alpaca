<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;

class LoginControllerTest extends TestCase
{
  /**
   * A user can log in
   *
   * @test
   * @return void
   */
  public function user_can_login()
  {
    $password = "12345678";
    $user = factory(User::class)->create();
    $this->assertTrue(Hash::check($password, $user->password));
    $response = $this
      ->post(route('login.login'), [
      'email' => $user->email,
      'password' => $password
    ]);
    $user = User::find($user->id);
    $this->assertArrayHasKey('user', $response->json());
    $response->assertJsonFragment([
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'created_at' => $user->created_at,
      'updated_at' => $user->updated_at,
      'deleted_at' => null,
      'permissions' => [],
      'last_login' => $user->last_login,
      'active' => $user->active,
      'avatar' => null
     ]);
    $this->assertArrayHasKey('token',$response->json());
    $response->assertOk();
  }

  /**
   * A user can log out
   *
   * @test
   * @return void
   */
  public function user_can_logout()
  {
    $user = factory(User::class)->create();
    $token = $user->tokens()->first();
    Passport::actingAs($user);
    $response = $this
      ->get(route('login.logout'));
    $response->assertJson([
      "message" => "Resource found.",
      "data" => "Logged out."
    ]);
    $response->assertOk();
  }

  /**
   * A non-logged in user can log out
   *
   * @test
   * @return void
   */
  public function guest_can_logout()
  {
    $user = factory(User::class)->create();
    $token = $user->tokens()->first();
    $response = $this
      ->get(route('login.logout'));
    $response->assertNoContent();
  }
}
