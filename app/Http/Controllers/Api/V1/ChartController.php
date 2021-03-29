<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Task;
use App\Timer;

use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
  protected $defaultDays = 7;
  /**
   * menucontroller constructor.
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Get time logged by day
   *
   * @return Array
   */
  public function byDay(Request $request)
  {
    $start = $request['start'] ?? new \DateTime(date('Y-m-d', strtotime("-{$defaultDays} days")));
    $end = $request['end'] ?? new \DateTime('NOW');
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timeByDay = Timer::byDay($start, $end, $userIds)->toArray();
    $times = [];
    foreach ($timeByDay as $day) {
      $times[] = [$day['timer_date'], $day['duration']];
    }
    return $times;
  }

  /**
   * Get time logged by tag
   *
   * @return Array
   */
  public function byTag(Request $request)
  {
    $start = $request['start'] ?? new \DateTime(date('Y-m-d', strtotime("-{$defaultDays} days")));
    $end = $request['end'] ?? new \DateTime('NOW');
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timeByTag = Timer::byTag($start, $end, $userIds)->toArray();
    $times = [];
    foreach ($timeByTag as $tag) {
      $times[] = [$tag['tag_name'], $tag['duration']];
    }
    return $times;
  }

  /**
   * Get time logged by timer
   *
   * @return Array
   */
  public function byTimer(Request $request)
  {
    $start = $request['start'] ?? new \DateTime(date('Y-m-d', strtotime("-{$defaultDays} days")));
    $end = $request['end'] ?? new \DateTime('NOW');
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timers = Timer::byTimer($start, $end, $userIds);
    return $timers;
  }
}
