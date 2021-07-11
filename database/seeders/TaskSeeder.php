<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Timer;

class TaskSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $counter = 50;
    $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
    $end = new \DateTime('NOW');
    $defaultUserId = 1;
    $tags = ['project', 'testing', 'break'];

    for ($i = 0; $i < $counter; $i++) {
      $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
      $randomDate = new \DateTime();
      $randomDate->setTimestamp($randomTimestamp);
      $task = new Task();
      $task->name = "Task name ".uniqid(). " $i";
      $task->status = Task::STATUSES[mt_rand(0, 2)];
      $task->due = $randomDate->format("Y-m-d H:i:s");
      $task->user_id = $defaultUserId;
      $task->save();
      $taskCount = mt_rand(1, 5);
      for ($k = 1; $k < $taskCount; $k++) {
        $timer = new Timer();
        $timer->name = "Timer ".uniqid(). " $k";
        $timer->task_id = $task->id;
        $timer->user_id = $defaultUserId;
        $randomTimerTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomTimerDate = new \DateTime();
        $randomTimerDate->setTimestamp($randomTimerTimestamp);
        $timer->created_at = $randomDate->format("Y-m-d H:i:s");
        $timer->started_at = Carbon::createFromFormat('Y-m-d H:s:i', $task->due);
        $timer->stopped_at = $timer->started_at->addMinutes($k*5);
        $timer->duration = $timer->stopped_at->diffInSeconds($timer->started_at);
        $timer->save();
        $timer->tag([$tags[mt_rand(0, 2)]]);
        $timer->save();
      }
    }
  }
}
