<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Group;

class GroupControllerTest extends TestCase
{
    /**
     * List all groups
     *
     * @test
     * @return void
     */
    public function list_all_groups()
    {
      // Default groups have been created
      // from default seeds
      $this->assertDatabaseHas('groups', [
        'name' => 'Super User',
      ]);
      $this->assertDatabaseHas('groups', [
        'name' => 'User',
      ]);

      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('groups.index'));
      $response->assertOk();

      $group = factory(Group::class)->create();
      $this->assertDatabaseCount('groups', 3);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('groups.index'));
      $response->assertOk();
      $this->assertEquals(count($response['data']['data']), 3);
      $this->assertEquals($response['message'], 'Get groups ok.');
    }

    /**
     * Guest user not allowed to list groups
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_list_groups()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get(route('groups.index'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new group
     *
     * @test
     * @return void
     */
    public function save_new_group()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('groups.store'), [
          'name' => 'Test group 1',
          'permissions' => []
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test group 1');

      $this->assertDatabaseHas('groups', [
        'name' => 'Test group 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new group
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_group()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->post(route('groups.store'), [
          'name' => 'Test group 2',
          'permissions' => []
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find group
     *
     * @test
     * @return void
     */
    public function find_group()
    {
      $group = factory(Group::class)->create();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('groups.show', $group->id));
      $response->assertOk();
      $this->assertDatabaseHas('groups', [
        'name' => $group->name,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $group->name);
    }

    /**
     * Guest user allowed to find a group
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_find_group()
    {
      $group = factory(Group::class)->create();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('groups.show', $group->id));
      $response->assertOk();
      $this->assertDatabaseHas('groups', [
        'name' => $group->name,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $group->name);
    }

    /**
     * Update group
     *
     * @test
     * @return void
     */
    public function update_group()
    {
      $user = factory(User::class)->create();
      $group = factory(Group::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('groups.update', $group->id), [
          'name' => 'Test group 3',
          'permissions' => []
        ]);
      $response->assertOk();
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('groups.update', $group->id), [
          'name' => 'Test group 3 a',
          'permissions' => []
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('groups', [
        'name' => 'Test group 3 a',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test group 3 a');
    }

    /**
     * Guest user cannot update group
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_group()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->put(route('groups.update', '1'), [
          'name' => 'Test group 4',
          'permissions' => []
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete group
     *
     * @test
     * @return void
     */
    public function delete_group()
    {
      $user = factory(User::class)->create();
      $group = factory(Group::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('groups.destroy', $group->id));
      $response->assertOk();
      $this->assertDatabaseMissing('groups', [
        'name' => $group->name,
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete group
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_group()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->delete(route('groups.destroy', '1'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }
}
