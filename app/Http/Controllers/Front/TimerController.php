<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Timer;
use App\Task;

class TimerController extends FrontController
{
  public function store(Request $request, int $id)
  {
    $data = $request->validate(['name' => 'required|between:2,100']);
    $tags = $request['tags'];

    $tz = $request->tz ?? config('app.timezone');
    $now = Carbon::now(new \DateTimeZone($tz));
    $timer = new Timer();
    $timer->name = $data['name'];
    $timer->user_id = Auth::user()->id;
    $timer->started_at = $now;
    $task = Task::mine()
      ->findOrFail($id)
      ->timers()
      ->save($timer);
    if (!empty($tags)) {
      $timer->tag($tags);
      $timer->save();
    }

    return $this->sendResponseOk($timer->with(['task', 'tagged'])->find($timer->id), "Created.");
  }

  public function running()
  {
    $runningTimer = Timer::with(['task', 'tagged'])->mine()->running()->first() ?? [];
    return $this->sendResponseOk($runningTimer, "Running timer found ok");
  }

  public function stopRunning(Request $request)
  {
    if ($timer = Timer::mine()->running()->first()) {
      $start = Carbon::createFromFormat('Y-m-d H:s:i', $timer->started_at);
      $tz = $request->tz ?? config('app.timezone');
      $now = Carbon::now(new \DateTimeZone($tz));
      $timer->duration = $now->diffInSeconds($start);
      $timer->update(['stopped_at' => $now]);
    }

    return $this->sendResponseOk($timer, "Timer stopped ok");
  }

  public function delete(Request $request, int $id, int $timer_id)
  {
    Timer::destroy($timer_id);
    return $this->sendResponseOk([], "Timer deleted");
  }

  public function update(Request $request, int $id, int $timer_id)
  {
    $data = $request->validate([
      'name' => 'required|between:2,100',
      'started_at' => 'date',
      'stopped_at' => 'date'
    ]);
    $data['tags'] = $request->tags;
    return $this->sendResponseOk(Timer::put($timer_id, $data), "Timer updated ok");
  }
}
