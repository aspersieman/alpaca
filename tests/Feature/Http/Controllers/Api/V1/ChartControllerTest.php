<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;
use App\Models\Timer;

class ChartControllerTest extends TestCase
{
    /**
     * Chart by day
     * @test
     * @return void
     */
    public function chart_by_day()
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
      $start = new \DateTime(date('Y-m-d H:i:s', strtotime('-1460 minutes')));
      $stop = new \DateTime(date('Y-m-d H:i:s', strtotime('-1440 minutes')));
      $timers2 = factory(Timer::class, 3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'started_at' => $start->format('Y-m-d H:i:s'),
        'stopped_at' => $start->format('Y-m-d H:i:s'),
      ]);
      $timers3 = factory(Timer::class, 4)->create([
        'user_id' => $user2->id,
        'task_id' => $task2->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('charts.byDay'));
      $response->assertOk();

      $this->assertEquals(count($response['data']), 2);
      $response->assertJson([
        'message' => 'Resource found.',
        'data' => [
          [$start->format('Y-m-d'), 3600],
          [(new \DateTime('NOW'))->format('Y-m-d'), 3600]
        ],
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
    }

    /**
     * Chart by tag
     * @test
     * @return void
     */
    public function chart_by_tag()
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
      foreach($timers as $timer) {
        $timer->tag(['one']);
        $timer->save();
      }
      $start = new \DateTime(date('Y-m-d H:i:s', strtotime('-1460 minutes')));
      $stop = new \DateTime(date('Y-m-d H:i:s', strtotime('-1440 minutes')));
      $timers2 = factory(Timer::class, 3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'started_at' => $start->format('Y-m-d H:i:s'),
        'stopped_at' => $start->format('Y-m-d H:i:s'),
      ]);
      foreach($timers2 as $timer) {
        $timer->tag(['two']);
        $timer->save();
      }
      $timers3 = factory(Timer::class, 4)->create([
        'user_id' => $user2->id,
        'task_id' => $task2->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('charts.byTag'));
      $response->assertOk();

      $this->assertEquals(count($response['data']), 2);
      $response->assertJson([
        'message' => 'Resource found.',
        'data' => [
          ['One', 3600],
          ['Two', 3600]
        ],
      ]);
      $this->assertEquals($response['message'], 'Resource found.');
    }

    /**
     * Chart by timer
     * @test
     * @return void
     */
    public function chart_by_timer()
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
      $start = new \DateTime(date('Y-m-d H:i:s', strtotime('-1460 minutes')));
      $stop = new \DateTime(date('Y-m-d H:i:s', strtotime('-1440 minutes')));
      $timers2 = factory(Timer::class, 3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'started_at' => $start->format('Y-m-d H:i:s'),
        'stopped_at' => $start->format('Y-m-d H:i:s'),
      ]);
      $timers3 = factory(Timer::class, 4)->create([
        'user_id' => $user2->id,
        'task_id' => $task2->id
      ]);
      $response = $this
        ->actingAs($user, 'api')
        ->post(route('charts.byTimer'));
      $response->assertOk();

      $this->assertEquals(count($response['data']), 6);
      $this->assertEquals($response['message'], 'Resource found.');
    }
}
