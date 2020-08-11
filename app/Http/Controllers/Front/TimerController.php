<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Timer;
use App\Task;

class TimerController extends Controller
{
  public function store(Request $request, int $id)
  {
    $data = $request->validate(['name' => 'required|between:3,100']);

    $tz = $request->tz ?? config('app.timezone');
    $now = Carbon::now(new \DateTimeZone($tz));
    $timer = Task::mine()
      ->findOrFail($id)
      ->timers()
      ->save(new Timer([
        'name' => $data['name'],
        'user_id' => Auth::user()->id,
        'started_at' => $now,
      ]));

    return $timer->with('task')->find($timer->id);
  }

  public function running()
  {
    return Timer::with('task')->mine()->running()->first() ?? [];
  }

  public function stopRunning(Request $request)
  {
    if ($timer = Timer::mine()->running()->first()) {
      $tz = $request->tz ?? config('app.timezone');
      $now = Carbon::now(new \DateTimeZone($tz));
      $timer->update(['stopped_at' => $now]);
    }

    return $timer;
  }

  public function delete(Request $request, int $id, int $timer_id)
  {
    Timer::destroy($timer_id);
    return ["status"=>"success"];
  }
}
