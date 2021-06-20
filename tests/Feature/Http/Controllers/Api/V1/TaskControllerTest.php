<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    /**
     * List all own tasks
     *
     * @test
     * @return void
     */
    public function list_all_own_tasks()
    {
      $user = factory(User::class)->create();
      $user2 = factory(User::class)->create();
      $tasks = factory(Task::class, 3)->create([
        'user_id' => $user->id
      ]);
      $tasks = factory(Task::class, 4)->create([
        'user_id' => $user2->id
      ]);
      $this->assertDatabaseCount('tasks', 7);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('tasks.index'));
      $response->assertOk();

      // Only lists own tasks
      $this->assertEquals(count($response['data']), 3);
      $this->assertEquals($response['message'], 'List resource ok.');
    }

    /**
     * Guest user not allowed to list tasks
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_list_tasks()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $response = $this
        ->get(route('tasks.index'));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new task
     *
     * @test
     * @return void
     */
    public function save_new_task()
    {
      $user = factory(User::class)->create();
      $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
      $end = new \DateTime('NOW');
      $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
      $randomDate = new \DateTime();
      $randomDate->setTimestamp($randomTimestamp);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('tasks.store'), [
          'name' => 'Test task 1',
          'status' => Task::STATUSES[mt_rand(0, 2)],
          'due' => $randomDate->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'project_id' => null,
          'priority' => Task::PRIORITIES[mt_rand(0, 3)]
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test task 1');

      $this->assertDatabaseHas('tasks', [
        'name' => 'Test task 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new task
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_task()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
      $end = new \DateTime('NOW');
      $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
      $randomDate = new \DateTime();
      $randomDate->setTimestamp($randomTimestamp);
      $response = $this
        ->post(route('tasks.store'), [
          'name' => 'Test task 2',
          'status' => Task::STATUSES[mt_rand(0, 2)],
          'due' => $randomDate->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'project_id' => null,
          'priority' => Task::PRIORITIES[mt_rand(0, 3)]
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find task
     *
     * @test
     * @return void
     */
    public function find_task()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('tasks.show', $task->id));
      $response->assertOk();
      $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $task->name);
    }

    /**
     * GuesGuest not allowed to find a task
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_find_task()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->get(route('tasks.show', $task->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update task
     *
     * @test
     * @return void
     */
    public function update_task()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('tasks.update', $task->id), [
          'name' => 'Test task 3',
          'status' => Task::STATUSES[mt_rand(0, 2)],
          'due' => (new \DateTime('NOW'))->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'project_id' => null,
          'priority' => Task::PRIORITIES[mt_rand(0, 3)]
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('tasks', [
        'name' => 'Test task 3',
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->patch(route('tasks.update', $task->id), [
          'name' => 'Test task 3 a',
          'status' => Task::STATUSES[mt_rand(0, 2)],
          'due' => (new \DateTime('NOW'))->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'project_id' => null,
          'priority' => Task::PRIORITIES[mt_rand(0, 3)]
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('tasks', [
        'name' => 'Test task 3 a',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test task 3 a');
    }

    /**
     * Guest user cannot update task
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_task()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->put(route('tasks.update', $user->id), [
          'name' => 'Test task 3',
          'status' => Task::STATUSES[0],
          'due' => (new \DateTime('NOW'))->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'project_id' => null,
          'priority' => Task::PRIORITIES[0]
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete task
     *
     * @test
     * @return void
     */
    public function delete_task()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id,
        'name' => 'Test task 4'
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('tasks.destroy', $task->id));
      $response->assertOk();
      $this->assertDatabaseMissing('tasks', [
          'name' => 'Test task 4',
          'deleted_at' => null
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete task
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_task()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id,
        'name' => 'Test task 5'
      ]);
      $response = $this
        ->delete(route('tasks.destroy', $task->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }
}
