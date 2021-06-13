<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    /**
     * List all users
     *
     * @test
     * @return void
     */
    public function list_all_users()
    {
      // Default user has been created
      // from default seeds
      $this->assertDatabaseHas('users', [
        'email' => 'aspersieman@gmail.com',
      ]);

      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get($this->apiBaseUrl.'users');
      $response->assertOk();

      $users = factory(User::class, 3)->create();
      $this->assertDatabaseCount('users', 5);

      $response = $this
        ->actingAs($user, 'api')
        ->get($this->apiBaseUrl.'users');
      $this->assertEquals(count($response['data']['data']), 5);
      $this->assertEquals($response['message'], 'list users ok.');
    }

    /**
     * Unauthorised access not allowed to list users
     *
     * @test
     * @return void
     */
    public function unauthorised_access_not_allowed_to_list_users()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get($this->apiBaseUrl.'users');
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new user
     *
     * @test
     * @return void
     */
    public function save_new_user()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post($this->apiBaseUrl.'users', [
          "name" => "Test user 1",
          "email" => "test1@test.test",
          "password" => "test1234",
          "nickname" => "test1",
          "avatar" => null,
          "remember_token" => null,
          "permissions" => [],
          "active" => null,
          "activation_key" => null
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test user 1');

      $this->assertDatabaseHas('users', [
        'email' => 'test1@test.test',
      ]);
    }

    /**
     * Unauthorised access not allowed to save a new user
     *
     * @test
     * @return void
     */
    public function unauthorised_access_not_allowed_to_save_new_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->post($this->apiBaseUrl.'users', [
          "name" => "Test user 1",
          "email" => "test1@test.test",
          "password" => "test1234",
          "nickname" => "test1",
          "avatar" => null,
          "remember_token" => null,
          "permissions" => [],
          "active" => null,
          "activation_key" => null
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find user
     *
     * @test
     * @return void
     */
    public function find_user()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get($this->apiBaseUrl.'users/'.$user->id);
      $response->assertOk();
      $this->assertDatabaseHas('users', [
        'email' => $user->email,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $user->name);
      $this->assertEquals($response['data']['email'], $user->email);
    }

    /**
     * Unauthorised access not allowed to find a user
     *
     * @test
     * @return void
     */
    public function unauthorised_access_not_allowed_to_find_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get($this->apiBaseUrl.'users/1');
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update user
     *
     * @test
     * @return void
     */
    public function update_user()
    {
      $storage = Storage::fake('public');
      $user = factory(User::class)->create();
      $file = UploadedFile::fake()->image('avatar.jpg');
      $response = $this
        ->actingAs($user, 'api')
        ->put($this->apiBaseUrl.'users/'.$user->id, [
          'name' => 'Test user 3',
          'email' => 'test3@test.test',
          'permissions' => [],
          'groups' => [],
          'avatar_image' => $file,
          'nickname' => 'test3',
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('users', [
        'email' => 'test3@test.test',
        'nickname' => 'test3',
      ]);
      Storage::disk('public')->assertExists('avatar/'.$file->hashName());
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test user 3');
      $this->assertEquals($response['data']['email'], 'test3@test.test');
    }

    /**
     * Unauthorised user cannot update user
     *
     * @test
     * @return void
     */
    public function unauthorised_user_cannot_update_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->put($this->apiBaseUrl.'users/1', [
          'name' => 'Test user 3',
          'email' => 'test3@test.test',
          'permissions' => [],
          'groups' => [],
          'nickname' => 'test3',
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update user
     *
     * @test
     * @return void
     */
    public function delete_user()
    {
      $user = factory(User::class)->create();
      $userDelete = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete($this->apiBaseUrl.'users/'.$userDelete->id);
      $response->assertOk();
      $this->assertDatabaseMissing('users', [
          "name" => "Test user 4",
          "email" => "test4@test.test",
          "nickname" => "test4",
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Unauthorised user cannot delete user
     *
     * @test
     * @return void
     */
    public function unauthorised_user_cannot_delete_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->delete($this->apiBaseUrl.'users/1');
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * User cannot delete self
     *
     * @test
     * @return void
     */
    public function user_cannot_delete_self()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete($this->apiBaseUrl.'users/'.$user->id);
      $response->assertForbidden();
      $this->assertEquals($response['message'], 'Action forbidden.');
    }
}
