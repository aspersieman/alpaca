<?php

use Illuminate\Database\Seeder;
use App\Task;

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
    $start = new DateTime(date('Y-m-d', strtotime('-10 days')));
    $end = new DateTime('NOW');
    $defaultUserId = 1;

    for ($i=0; $i < $counter; $i++) {
      $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
      $randomDate = new DateTime();
      $randomDate->setTimestamp($randomTimestamp);
      $task = new Task();
      $task->name = "Task name ".uniqid(). " $i";
      $task->status = Task::STATUSES[mt_rand(0, 2)];
      $task->due = $randomDate->format("Y-m-d H:i:s");
      $task->user_id = $defaultUserId;
      $task->save();
    }
  }
}
