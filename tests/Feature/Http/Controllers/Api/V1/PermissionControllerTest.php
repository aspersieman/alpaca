<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;

class PermissionControllerTest extends TestCase
{
    /**
     * List all permissions
     *
     * @test
     * @return void
     */
    public function list_all_permissions()
    {
      // Default permissions have been created
      // from default seeds
      $this->assertDatabaseHas('permissions', [
        'title' => 'Super User',
        'description' => 'Superuser permission',
        'key' => 'superuser'
      ]);
      $this->assertDatabaseHas('permissions', [
        'title' => 'User Create',
        'description' => 'Permission to create user.',
        'key' => 'user.create'
      ]);
      $this->assertDatabaseHas('permissions', [
        'title' => 'User Edit',
        'description' => 'Permission to edit user.',
        'key' => 'user.edit'
      ]);
      $this->assertDatabaseHas('permissions', [
        'title' => 'User Delete',
        'description' => 'Permission to delete user.',
        'key' => 'user.delete'
      ]);

      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('permissions.index'));
      $response->assertOk();

      $permission = factory(Permission::class)->create();
      $this->assertDatabaseCount('permissions', 5);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('permissions.index'));
      $response->assertOk();
      $this->assertEquals(count($response['data']['data']), 5);
      $this->assertEquals($response['message'], 'Get permissions ok.');
    }

    /**
     * Guest user not allowed to list permissions
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_list_permissions()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get(route('permissions.index'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new permission
     *
     * @test
     * @return void
     */
    public function save_new_permission()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('permissions.store'), [
          'title' => 'Test permission 1',
          'description' => 'Test permission 1 description',
          'key' => 'test.permission.1',
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['title'], 'Test permission 1');

      $this->assertDatabaseHas('permissions', [
        'title' => 'Test permission 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new permission
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_permission()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->post(route('permissions.store'), [
          'title' => 'Test permission 2',
          'description' => 'Test permission 2 description',
          'key' => 'test.permission.2',
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find permission
     *
     * @test
     * @return void
     */
    public function find_permission()
    {
      $permission = factory(Permission::class)->create();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('permissions.show', $permission->id));
      $response->assertOk();
      $this->assertDatabaseHas('permissions', [
        'title' => $permission->title,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['title'], $permission->title);
      $this->assertEquals($response['data']['description'], $permission->description);
      $this->assertEquals($response['data']['key'], $permission->key);
    }

    /**
     * Guest user allowed to find a permission
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_find_permission()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $permission = factory(Permission::class)->create();
      $response = $this
        ->get(route('permissions.show', $permission->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update permission
     *
     * @test
     * @return void
     */
    public function update_permission()
    {
      $user = factory(User::class)->create();
      $permission = factory(Permission::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('permissions.update', $permission->id), [
          'title' => 'Test permission 3',
          'description' => 'Test permission 3 description',
          'key' => 'test.permission.3',
        ]);
      $response->assertOk();
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('permissions.update', $permission->id), [
          'title' => 'Test permission 3',
          'description' => 'Test permission 3 description a',
          'key' => 'test.permission.3',
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('permissions', [
        'title' => 'Test permission 3',
        'description' => 'Test permission 3 description a',
        'key' => 'test.permission.3',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['title'], 'Test permission 3');
      $this->assertEquals($response['data']['description'], 'Test permission 3 description a');
      $this->assertEquals($response['data']['key'], 'test.permission.3');
    }

    /**
     * Guest user cannot update permission
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_permission()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->put(route('permissions.update', '1'), [
          'title' => 'Test permission 4',
          'description' => 'Test permission 4 description',
          'key' => 'test.permission.4',
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete permission
     *
     * @test
     * @return void
     */
    public function delete_permission()
    {
      $user = factory(User::class)->create();
      $permission = factory(Permission::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('permissions.destroy', $permission->id));
      $response->assertOk();
      $this->assertDatabaseMissing('permissions', [
        'title' => $permission->title,
        'description' => $permission->description,
        'key' => $permission->key,
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete permission
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_permission()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->delete(route('permissions.destroy', '1'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }
}
