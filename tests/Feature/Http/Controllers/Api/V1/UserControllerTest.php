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
        ->get(route('users.index'));
      $response->assertOk();

      $users = factory(User::class, 3)->create();
      $this->assertDatabaseCount('users', 5);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('users.index'));
      $this->assertEquals(count($response['data']['data']), 5);
      $this->assertEquals($response['message'], 'List users ok.');
    }

    /**
     * Guest user not allowed to list users
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_list_users()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get(route('users.index'));
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
        ->post(route('users.store'), [
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
     * Guest user not allowed to save a new user
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->post(route('users.store'), [
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
        ->get(route('users.show', $user->id));
      $response->assertOk();
      $this->assertDatabaseHas('users', [
        'email' => $user->email,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $user->name);
      $this->assertEquals($response['data']['email'], $user->email);
    }

    /**
     * GuesGuest not allowed to find a user
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_find_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get(route('users.show','1'));
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
        ->put(route('users.update', $user->id), [
          'name' => 'Test user 3',
          'email' => 'test3@test.test',
          'permissions' => [],
          'groups' => [],
          'avatar_image' => $file,
          'nickname' => 'test3',
        ]);
      $response->assertOk();
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('users.update', $user->id), [
          'name' => 'Test user 3.a',
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
      $this->assertEquals($response['data']['name'], 'Test user 3.a');
      $this->assertEquals($response['data']['email'], 'test3@test.test');
    }

    /**
     * Guest user cannot update user
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->put(route('users.update', '1'), [
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
     * Delete user
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
        ->delete(route('users.destroy', $userDelete->id));
      $response->assertOk();
      $this->assertDatabaseMissing('users', [
          "name" => "Test user 4",
          "email" => "test4@test.test",
          "nickname" => "test4",
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete user
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_user()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->delete(route('users.destroy', '1'));
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
        ->delete(route('users.destroy', $user->id));
      $response->assertForbidden();
      $this->assertEquals($response['message'], 'Action forbidden.');
    }
}
