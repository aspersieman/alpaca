<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\Timer;
use App\Models\Note;

use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
  protected $defaultDays = 14;
  /**
   * menucontroller constructor.
   */
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /**
   * Get time logged by day
   *
   * @return Array
   */
  public function byDay(Request $request)
  {
    $start = $request['start'] ?? (new \DateTime(date('Y-m-d', strtotime("-{$this->defaultDays} days"))))->format("Y-m-d");
    $end = $request['end'] ?? (new \DateTime('NOW'))->format("Y-m-d");
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timeByDay = Timer::byDay($start, $end, $userIds)->toArray();
    $times = [];
    foreach ($timeByDay as $day) {
      $times[] = [$day['timer_date'], $day['duration']];
    }
    return $this->sendResponseOk($times);
  }

  /**
   * Get time logged by tag
   *
   * @return Array
   */
  public function byTag(Request $request)
  {
    $start = $request['start'] ?? (new \DateTime(date('Y-m-d', strtotime("-{$this->defaultDays} days"))))->format("Y-m-d");
    $end = $request['end'] ?? (new \DateTime('NOW'))->format("Y-m-d");
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timeByTag = Timer::byTag($start, $end, $userIds)->toArray();
    $times = [];
    foreach ($timeByTag as $tag) {
      $times[] = [$tag['tag_name'], $tag['duration']];
    }
    return $this->sendResponseOk($times);
  }

  /**
   * Get time logged by timer
   *
   * @return Array
   */
  public function byTimer(Request $request)
  {
    $start = $request['start'] ?? (new \DateTime(date('Y-m-d', strtotime("-{$this->defaultDays} days"))))->format("Y-m-d");
    $end = $request['end'] ?? (new \DateTime('NOW'))->format("Y-m-d");
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timers = Timer::byTimer($start, $end, $userIds);
    return $this->sendResponseOk($timers);
  }

  /**
   * Get count tasks logged by completed
   *
   * @return Array
   */
  public function byCompleted(Request $request)
  {
    $start = $request['start'] ?? (new \DateTime(date('Y-m-d', strtotime("-{$this->defaultDays} days"))))->format("Y-m-d");
    $end = $request['end'] ?? (new \DateTime('NOW'))->format("Y-m-d");
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $tasks = Task::select(
      [
        \DB::raw("DATE(completed_at) AS completed_at"),
        \DB::raw("COUNT(id) AS total")
      ]
    )
      ->whereNotNull('completed_at')
      ->whereIn('user_id', $userIds)
      ->where('completed_at', '>=', $start . " 00:00:00")
      ->where('completed_at', '<=', $end . " 23:59:59")
      ->groupBy(\DB::raw("DATE(completed_at)"))
      ->orderBy('completed_at', 'asc')
      ->get();
    return $this->sendResponseOk($tasks);
  }

  /**
   * Get time logged by timer
   *
   * @return Array
   */
  public function stats(Request $request)
  {
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $now = (new \DateTime('NOW'))->format("Y-m-d");
    $tasksTotal = Task::select(
      [
        \DB::raw("COUNT(id) AS total_task_count")
      ]
    )
      ->whereIn('user_id', $userIds)
      ->first();
    $tasksDue = Task::select(
      [
        \DB::raw("COUNT(id) AS due_task_count")
      ]
    )
      ->where('due', '<=', $now)
      ->where('status', '!=', 'complete')
      ->whereIn('user_id', $userIds)
      ->first();
    $timersTotal = Timer::select(
      [
        \DB::raw("SUM(duration) AS total_timer_duration")
      ]
    )
      ->whereNotNull('stopped_at')
      ->whereIn('user_id', $userIds)
      ->first();
    $notesTotal = Note::select(
      [
        \DB::raw("COUNT(id) AS total_notes_count")
      ]
    )
      ->whereIn('user_id', $userIds)
      ->first();
    return $this->sendResponseOk([
      'total_task_count' => $tasksTotal['total_task_count'],
      'due_task_count' => $tasksDue['due_task_count'],
      'total_timer_duration' => $timersTotal['total_timer_duration'],
      'total_notes_count' => $notesTotal['total_notes_count']
    ]);
  }
}
