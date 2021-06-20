<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;
use App\Models\File;
use App\Models\FileGroup;

class FileControllerTest extends TestCase
{
    /**
     * List all files
     *
     * @test
     * @return void
     */
    public function list_all_files()
    {
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $this->assertDatabaseCount('files', 1);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('files.index'));
      $response->assertOk();

      $this->assertEquals(count($response['data']['data']), 1);
      $this->assertEquals($response['message'], 'List resource ok.');
    }

    /**
     * Guest user allowed to list files
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_list_files()
    {
      $storage = Storage::fake('local');
      $file = factory(File::class)->create();
      $this->assertDatabaseCount('files', 1);

      $response = $this
        ->get(route('files.index'));
      $response->assertOk();

      $this->assertEquals(count($response['data']['data']), 1);
      $this->assertEquals($response['message'], 'List resource ok.');
      $response->assertJsonStructure([
        'data' => [
          'data' => [
            '*' => [
              'id',
              'name',
              'uploaded_by',
              'file_group_id',
              'file_type',
              'extension',
              'size',
              'path',
              'created_at',
              'updated_at',
              'file_token',
              'user',
              'group',
            ],
          ],
        ]
      ]);
    }

    /**
     * Save new file
     *
     * @test
     * @return void
     */
    public function save_new_file()
    {
      $storage = Storage::fake('local');
      $file = UploadedFile::fake()->image('test.jpg');
      $user = factory(User::class)->create();
      $fileGroup = factory(FileGroup::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('files.store'), [
          'file' => [$file],
          'file_group_id' => $fileGroup->id
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertDatabaseCount('files', 1);
      $this->assertDatabaseHas('files', [
        'name' => $file->name,
      ]);
    }

    /**
     * Guest user not allowed to save a new file
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_file()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $storage = Storage::fake('local');
      $file = UploadedFile::fake()->image('test.jpg');
      $fileGroup = factory(FileGroup::class)->create();
      $response = $this
        ->post(route('files.store'), [
          'file' => [$file],
          'file_group_id' => $fileGroup->id
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find file
     *
     * @test
     * @return void
     */
    public function find_file()
    {
      $storage = Storage::fake('local');
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $this->assertDatabaseCount('files', 1);

      $response = $this
        ->actingAs($user, 'api')
        ->get(route('files.show', $file->id));
      $response->assertOk();

      $this->assertEquals($response['message'], 'Resource found.');
      $response->assertJsonStructure([
        'data' => [
          'id',
          'name',
          'uploaded_by',
          'file_group_id',
          'file_type',
          'extension',
          'size',
          'path',
          'created_at',
          'updated_at',
          'file_token',
        ]
      ]);
    }

    /**
     * Guest user allowed to find a file
     *
     * @test
     * @return void
     */
    public function guest_user_allowed_to_find_file()
    {
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $this->assertDatabaseCount('files', 1);

      $response = $this
        ->get(route('files.show', $file->id));
      $response->assertOk();

      $this->assertEquals($response['message'], 'Resource found.');
      $response->assertJsonStructure([
        'data' => [
          'id',
          'name',
          'uploaded_by',
          'file_group_id',
          'file_type',
          'extension',
          'size',
          'path',
          'created_at',
          'updated_at',
          'file_token',
        ]
      ]);
    }

    /**
     * Update file
     *
     * @test
     * @return void
     */
    public function update_file()
    {
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('files.update', $file->id), []);
      $response->assertOk();
      $this->assertEquals($response['message'], 'Resource updated.');
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('files.update', $file->id), []);
      $response->assertOk();
      $this->assertEquals($response['message'], 'Resource updated.');
    }

    /**
     * Guest user cannot update file
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_file()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $file = factory(File::class)->create();
      $createResponse = $this
        ->put(route('files.update', $file->id), []);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
      $createResponse = $this
        ->patch(route('files.update', $file->id), []);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete file
     *
     * @test
     * @return void
     */
    public function delete_file()
    {
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('files.destroy', $file->id), []);
      $this->assertEquals($response['message'], 'Resource deleted.');
      $this->assertDatabaseMissing('files', [
        'id' => $file->id,
      ]);
    }

    /**
     * Guest user cannot delete file
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_file()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $file = factory(File::class)->create();
      $response = $this
        ->delete(route('files.destroy', $file->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Download file
     *
     * @test
     * @return void
     */
    public function download_file()
    {
      $user = factory(User::class)->create();
      $storage = Storage::fake('local');
      $file = UploadedFile::fake()->image('test.jpg');
      $fileGroup = factory(FileGroup::class)->create();
      $fileCreate = $this
        ->actingAs($user, 'api')
        ->post(route('files.store'), [
          'file' => [$file],
          'file_group_id' => $fileGroup->id
        ]);
      $fileCreate->assertCreated();
      $this->assertDatabaseCount('files', 1);
      $this->assertDatabaseHas('files', [
        'name' => $fileCreate['data']['name'],
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->call('GET', route('files.download', $fileCreate['data']['id']), [
          'file_token' => $fileCreate['data']['file_token']
        ]);
      $response->assertHeader('Content-Disposition', 'attachment; filename=test.jpg.jpg');
      $response->assertHeader('Content-Type', 'image/jpeg');
    }

    /**
     * Preview file
     *
     * @test
     * @return void
     */
    public function preview_file()
    {
      $user = factory(User::class)->create();
      $storage = Storage::fake('local');
      $file = UploadedFile::fake()->image('test.jpg');
      $fileGroup = factory(FileGroup::class)->create();
      $fileCreate = $this
        ->actingAs($user, 'api')
        ->post(route('files.store'), [
          'file' => [$file],
          'file_group_id' => $fileGroup->id
        ]);
      $fileCreate->assertCreated();
      $this->assertDatabaseCount('files', 1);
      $this->assertDatabaseHas('files', [
        'name' => $fileCreate['data']['name'],
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->call('GET', route('files.preview', $fileCreate['data']['id']), [
          'w' => 500,
          'h' => 500
        ]);
      $response->assertHeader('Content-Type', 'image/jpeg');
    }
}
