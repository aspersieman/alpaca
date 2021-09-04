<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Timer;
use App\Models\Task;
use App\Http\Requests\SaveTimerRequest;
use App\Http\Requests\UpdateTimerRequest;
use App\Http\Resources\TimerCollection;
use App\Filters\TimerFilter;

class TimerController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(TimerFilter $filter)
  {
    $timers = Timer::with(['user', 'tagged'])
      ->filter($filter)
      ->mine()
      ->orderBy('started_at', 'asc')
      ->get();
    return $this->sendResponseOk(
      new TimerCollection($timers),
      "List resource ok."
    );
  }

  public function store(SaveTimerRequest $request, int $id)
  {
    $request->merge(['user_id' => auth()->user()->id]);
    $tags = $request['tags'];

    $tz = $request->tz ?? config('app.timezone');
    $now = Carbon::now(new \DateTimeZone($tz));
    $timer = new Timer();
    $timer->name = $request['name'];
    $timer->user_id = $request['user_id'];
    $timer->started_at = $now;
    $task = Task::mine()
      ->findOrFail($id)
      ->timers()
      ->save($timer);
    if (!empty($tags)) {
      $timer->tag($tags);
      $timer->save();
    }

    return $this->sendResponseCreated($timer->with(['task', 'tagged'])->find($timer->id), "Resource created.");
  }

  /**
   * Display the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function show(int $id, int $timer_id)
  {
    $timer = Timer::with(['user', 'tagged'])
      ->find($timer_id);

    if(!$timer) return $this->sendResponseNotFound();

    return $this->sendResponseOk(
      $timer,
      "Resource found."
    );
  }

  public function update(UpdateTimerRequest $request, int $id, int $timer_id)
  {
    $request->merge(['user_id' => auth()->user()->id]);
    $tags = $request->tags;

    $timer = Timer::find($timer_id);
    $timer->name = $request['name'];
    $timer->user_id = $request['user_id'];
    $tz = $request->tz ?? config('app.timezone');
    $startedAt = new Carbon(new \DateTime($request['started_at']));
    $stoppedAt = new Carbon(new \DateTime($request['stopped_at']));
    $timer->started_at = $startedAt->format("Y-m-d H:i:s");
    $timer->stopped_at = $stoppedAt->format("Y-m-d H:i:s");
    $duration = $stoppedAt->diffInSeconds($startedAt);
    $timer->duration = $duration;
    if (!empty($tags)) {
      $timer->tag($tags);
    }
    $timer->save();
    return $this->sendResponseOk($timer, "Resource updated.");
  }

  public function running()
  {
    $runningTimer = Timer::with(['tagged', 'task'])->mine()->running()->first() ?? [];
    return $this->sendResponseOk($runningTimer, "Resource found.");
  }

  public function stop(Request $request)
  {
    if ($timer = Timer::mine()->running()->first()) {
      $tz = $request->tz ?? config('app.timezone');
      $start = Carbon::createFromFormat('Y-m-d H:i:s', $timer->started_at, $tz);
      $now = Carbon::now(new \DateTimeZone($tz));
      $duration = $now->diffInSeconds($start);
      $timer->stopped_at = $now;
      $timer->duration = $duration;
      $timer->save();
    }

    return $this->sendResponseOk($timer, "Resource updated.");
  }

  public function delete(Request $request, int $id, int $timer_id)
  {
    Timer::destroy($timer_id);
    return $this->sendResponseOk([], "Resource deleted.");
  }
}
