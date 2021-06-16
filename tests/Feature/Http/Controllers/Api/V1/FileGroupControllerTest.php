<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\FileGroup;

class FileGroupControllerTest extends TestCase
{
    /**
     * List all file groups
     *
     * @test
     * @return void
     */
    public function list_all_file_groups()
    {
      // Default file_group has been created
      // from default seeds
      $this->assertDatabaseHas('file_groups', [
        'name' => 'General',
      ]);

      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('file-groups.index'));
      $response->assertOk();

      $fileGroup = factory(FileGroup::class)->create();
      $this->assertDatabaseCount('file_groups', 2);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('file-groups.index'));
      $response->assertOk();
      $this->assertEquals(count($response['data']['data']), 2);
      $this->assertEquals($response['message'], 'Get file groups ok.');
    }

    /**
     * Guest user allowed to list file groups
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_list_file_groups()
    {
      $response = $this
        ->get(route('file-groups.index'));
      $response->assertOk();
      $this->assertEquals(count($response['data']['data']), 1);
      $this->assertEquals($response['message'], 'Get file groups ok.');
    }

    /**
     * Save new file group
     *
     * @test
     * @return void
     */
    public function save_new_file_group()
    {
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('file-groups.store'), [
          'name' => 'Test file group 1',
          'description' => 'Test file group 1 description',
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test file group 1');

      $this->assertDatabaseHas('file_groups', [
        'name' => 'Test file group 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new file group
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_file_group()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->post(route('file-groups.store'), [
          'name' => 'Test file group 2',
          'description' => 'Test file group 2 description',
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find file group
     *
     * @test
     * @return void
     */
    public function find_file_group()
    {
      $fileGroup = factory(FileGroup::class)->create();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('file-groups.show', $fileGroup->id));
      $response->assertOk();
      $this->assertDatabaseHas('file_groups', [
        'name' => $fileGroup->name,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $fileGroup->name);
      $this->assertEquals($response['data']['description'], $fileGroup->description);
    }

    /**
     * Guest user allowed to find a file group
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_find_file_group()
    {
      $fileGroup = factory(FileGroup::class)->create();
      $user = factory(User::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('file-groups.show', $fileGroup->id));
      $response->assertOk();
      $this->assertDatabaseHas('file_groups', [
        'name' => $fileGroup->name,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $fileGroup->name);
      $this->assertEquals($response['data']['description'], $fileGroup->description);
    }

    /**
     * Update file group
     *
     * @test
     * @return void
     */
    public function update_file_group()
    {
      $user = factory(User::class)->create();
      $fileGroup = factory(FileGroup::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('file-groups.update', $fileGroup->id), [
          'name' => 'Test file group 3',
          'description' => 'Test file group 3 description',
        ]);
      $response->assertOk();
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('file-groups.update', $fileGroup->id), [
          'name' => 'Test file group 3',
          'description' => 'Test file group 3 description a',
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('file_groups', [
        'name' => 'Test file group 3',
        'description' => 'Test file group 3 description a',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test file group 3');
      $this->assertEquals($response['data']['description'], 'Test file group 3 description a');
    }

    /**
     * Guest user cannot update file group
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_file_group()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->put(route('file-groups.update', '1'), [
          'name' => 'Test file group 4',
          'description' => 'Test file group 4 description',
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete file group
     *
     * @test
     * @return void
     */
    public function delete_file_group()
    {
      $user = factory(User::class)->create();
      $fileGroup = factory(FileGroup::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('file-groups.destroy', $fileGroup->id));
      $response->assertOk();
      $this->assertDatabaseMissing('file_groups', [
        'name' => $fileGroup->name,
        'description' => $fileGroup->description,
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete file-groups
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_file_groups()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->delete(route('file-groups.destroy', '1'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }
}
