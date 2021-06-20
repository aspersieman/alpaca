<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\SendPasswordResetLink;

class PasswordResetControllerTest extends TestCase
{
    /**
     * Send password reset link email
     * @test
     * @return void
     */
    public function send_password_reset_link_email()
    {
      Notification::fake();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('password.sendResetLinkEmail'), [
          'email' => $user->email,
        ]);
      $response->assertOk();
      Notification::assertSentTo(
        [$user], SendPasswordResetLink::class
      );
      $this->assertDatabaseHas('password_resets', [
        'email' => $user->email,
      ]);
    }

    /**
     * Reset password
     * @test
     * @return void
     */
    public function reset_password()
    {
      Notification::fake();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('password.sendResetLinkEmail'), [
          'email' => $user->email,
        ]);
      $response->assertOk();
      $passwordReset = PasswordReset::where(['email' => $user->email])->first();
      $password = Str::random(50);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('password.resetPassword'), [
          'email' => $user->email,
          'token' => $passwordReset->token,
          'password' => $password,
          'password_confirmation' => $password
        ]);
      $response->assertOk();
      $response->assertJsonStructure([
        'token',
        'user'
      ]);
    }
}
