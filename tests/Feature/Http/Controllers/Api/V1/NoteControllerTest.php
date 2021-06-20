<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;
use App\Models\Note;

class NoteControllerTest extends TestCase
{
    /**
     * List all own notes
     *
     * @test
     * @return void
     */
    public function list_all_own_notes()
    {
      $user = factory(User::class)->create();
      $user2 = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $task2 = factory(Task::class)->create([
        'user_id' => $user2->id
      ]);
      $notes = factory(Note::class, 3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $notes2 = factory(Note::class, 4)->create([
        'user_id' => $user2->id,
        'task_id' => $task2->id
      ]);
      $this->assertDatabaseCount('notes', 7);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('notes.index'));
      $response->assertOk();

      // Only lists own notes
      $this->assertEquals(count($response['data']), 3);
      $this->assertEquals($response['message'], 'List resource ok.');
    }

    /**
     * Guest user not allowed to list notes
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_list_notes()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->get(route('notes.index'), ['task_id' => $task->id]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new note
     *
     * @test
     * @return void
     */
    public function save_new_note()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('notes.store'), [
          'name' => 'Test note 1',
          'content' => 'Test note content 1',
          'user_id' => $user->id,
          'task_id' => $task->id
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test note 1');

      $this->assertDatabaseHas('notes', [
        'name' => 'Test note 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new note
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_note()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->post(route('notes.store'), [
          'name' => 'Test note 2',
          'content' => 'Test note content 2',
          'user_id' => $user->id,
          'task_id' => $task->id
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find note
     *
     * @test
     * @return void
     */
    public function find_note()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('notes.show', $note->id));
      $response->assertOk();
      $this->assertDatabaseHas('notes', [
        'id' => $note->id,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $note->name);
    }

    /**
     * GuesGuest not allowed to find a note
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_find_note()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->get(route('notes.show', $note->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update note
     *
     * @test
     * @return void
     */
    public function update_note()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('notes.update', $note->id), [
          'name' => 'Test note 5',
          'content' => 'Test note content 5'
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('notes', [
        'name' => 'Test note 5',
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('notes.update', $note->id), [
          'name' => 'Test note 5 a',
          'content' => 'Test note content 5 a'
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('notes', [
        'name' => 'Test note 5 a',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test note 5 a');
    }

    /**
     * Guest user cannot update note
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_note()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->put(route('notes.update', $note->id), [
          'name' => 'Test note 6',
          'content' => 'Test note content 6'
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete note
     *
     * @test
     * @return void
     */
    public function delete_note()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('notes.destroy', $note->id));
      $response->assertOk();
      $this->assertDatabaseMissing('notes', [
          'id' => $note->id,
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete note
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_note()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $note = factory(Note::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->delete(route('notes.destroy', $note->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }
}
