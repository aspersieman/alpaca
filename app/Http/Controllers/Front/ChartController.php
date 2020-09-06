<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Task;
use App\Timer;

class ChartController extends FrontController
{
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
   * @return \illuminate\http\response
   */
  public function byDay(Request $request)
  {
    $start = $request['start'] ?? new \DateTime(date('Y-m-d', strtotime('-20 days')));
    $end = $request['end'] ?? new \DateTime('NOW');
    $userIds = $request['userIds'] ?? [auth()->user()->id];
    $timeByDay = Timer::byDay($start, $end, $userIds)->toArray();
    $times = [];
    foreach ($timeByDay as $day) {
      $times[] = [$day['timer_date'], $day['duration']];
    }
    return $times;
  }
}
