<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;
use App\Models\Timer;

class TimerControllerTest extends TestCase
{
    /**
     * List all own timers
     *
     * @test
     * @return void
     */
    public function list_all_own_timers()
    {
      $user = factory(User::class)->create();
      $user2 = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $task2 = factory(Task::class)->create([
        'user_id' => $user2->id
      ]);
      $timers = factory(Timer::class, 3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $timers2 = factory(Timer::class, 4)->create([
        'user_id' => $user2->id,
        'task_id' => $task2->id
      ]);
      $this->assertDatabaseCount('timers', 7);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('timers.index', $task->id));
      $response->assertOk();

      // Only lists own timers
      $this->assertEquals(count($response['data']), 3);
      $this->assertEquals($response['message'], 'List resource ok.');
    }

    /**
     * Guest user not allowed to list timers
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_list_timers()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $response = $this
        ->get(route('timers.index', $task->id));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Save new timer
     *
     * @test
     * @return void
     */
    public function save_new_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
      $stop = new \DateTime('NOW');
      $tags = ['one', 'two', 'three'];
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('timers.store', $task->id), [
          'name' => 'Test timer 1',
          'started_at' => $start->format("Y-m-d H:i:s"),
          'stopped_at' => $stop->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'tags' => $tags
        ]);
      $response->assertCreated();
      $this->assertEquals($response['message'], 'Resource created.');
      $this->assertEquals($response['data']['name'], 'Test timer 1');

      $this->assertDatabaseHas('timers', [
        'name' => 'Test timer 1',
      ]);
    }

    /**
     * Guest user not allowed to save a new timer
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_save_new_timer()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
      $stop = new \DateTime('NOW');
      $tags = ['one', 'two', 'three'];
      $response = $this
        ->post(route('timers.store', $task->id), [
          'name' => 'Test timer 2',
          'started_at' => $start->format("Y-m-d H:i:s"),
          'stopped_at' => $stop->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
          'tags' => $tags
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Find timer
     *
     * @test
     * @return void
     */
    public function find_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'name' => 'Test timer 3',
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('timers.show', [ 'id' => $task->id, 'timer_id' => $timer->id ]));
      $response->assertOk();
      $this->assertDatabaseHas('timers', [
        'id' => $timer->id,
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], $timer->name);
    }

    /**
     * GuesGuest not allowed to find a timer
     *
     * @test
     * @return void
     */
    public function guest_user_not_allowed_to_find_timer()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'name' => 'Test timer 4',
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->get(route('timers.show', [ 'id' => $task->id, 'timer_id' => $timer->id ]));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Update timer
     *
     * @test
     * @return void
     */
    public function update_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'name' => 'Test timer 5',
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->put(route('timers.update', [ 'id' => $task->id, 'timer_id' => $timer->id ]), [
          'name' => 'Test timer 5 a',
          'started_at' => (new \DateTime('NOW'))->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
        ]);
      $response->assertOk();
      $this->assertDatabaseHas('timers', [
        'name' => 'Test timer 5 a',
      ]);
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test timer 5 a');
    }

    /**
     * Guest user cannot update timer
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_update_timer()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'name' => 'Test timer 5',
        'user_id' => $user->id,
        'task_id' => $task->id
      ]);
      $response = $this
        ->put(route('timers.update', [ 'id' => $task->id, 'timer_id' => $timer->id ]), [
          'name' => 'Test timer 5 a',
          'started_at' => (new \DateTime('NOW'))->format("Y-m-d H:i:s"),
          'user_id' => $user->id,
        ]);
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Delete timer
     *
     * @test
     * @return void
     */
    public function delete_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'name' => 'Test timer 6'
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->delete(route('timers.destroy', [ 'id' => $task->id, 'timer_id' => $timer->id ]));
      $response->assertOk();
      $this->assertDatabaseMissing('timers', [
          'name' => 'Test timer 6',
          'deleted_at' => null
      ]);
      $this->assertEquals($response['message'], 'Resource deleted.');
    }

    /**
     * Guest user cannot delete timer
     *
     * @test
     * @return void
     */
    public function guest_user_cannot_delete_timer()
    {
      $this->withoutExceptionHandling();
      $this->expectException('Illuminate\Auth\AuthenticationException');
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
      ]);
      $response = $this
        ->delete(route('timers.destroy', [ 'id' => $task->id, 'timer_id' => $timer->id ]));
      $response->assertUnauthorized();
      $this->assertEquals($response['message'], 'Unauthenticated.');
    }

    /**
     * Get running timer
     *
     * @test
     * @return void
     */
    public function running_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'name' => 'Test timer 7',
        'stopped_at' => null
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->get(route('timers.running'));
      $response->assertOk();
      $this->assertEquals($response['message'], 'Resource found.');
      $this->assertEquals($response['data']['name'], 'Test timer 7');
      $this->assertDatabaseHas('timers', [
        'name' => 'Test timer 7',
        'stopped_at' => null
      ]);
    }

    /**
     * Stop timer
     *
     * @test
     * @return void
     */
    public function stop_timer()
    {
      $user = factory(User::class)->create();
      $task = factory(Task::class)->create([
        'user_id' => $user->id
      ]);
      $timer = factory(Timer::class)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'name' => 'Test timer 8',
        'stopped_at' => null
      ]);
      $this->assertDatabaseHas('timers', [
        'name' => 'Test timer 8',
        'stopped_at' => null
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('timers.stop', $task->id));
      $response->assertOk();
      $this->assertEquals($response['message'], 'Resource updated.');
      $this->assertEquals($response['data']['name'], 'Test timer 8');
      $date = new \DateTime($response['data']['stopped_at']);
      $date->setTimezone(new \DateTimeZone(env('APP_TIMEZONE')));
      $this->assertDatabaseHas('timers', [
        'name' => 'Test timer 8',
        'stopped_at' => $date,
      ]);
    }
}
