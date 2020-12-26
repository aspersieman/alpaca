<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Conner\Tagging\Taggable;

class Timer extends Model
{
  use \Conner\Tagging\Taggable;
  use SoftDeletes;

  /**
   * {@inheritDoc}
   */
  protected $fillable = [
    'name', 'user_id', 'task_id', 'stopped_at', 'started_at', 'tags' 
  ];

  /**
   * {@inheritDoc}
   */
  protected $dates = ['started_at', 'stopped_at'];

  /**
   * {@inheritDoc}
   */
  protected $with = ['user'];

  /**
   * Get the related user.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the related task
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  /**
   * Get timer for current user.
   *
   * @param  \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeMine($query)
  {
    return $query->whereUserId(auth()->user()->id);
  }

  /**
   * Get the running timers
   *
   * @param  \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeRunning($query)
  {
    return $query->whereNull('stopped_at');
  }

  public static function put($id, $data)
  {
    $timer = Timer::where('id', $id)->first();
    $timer->name = $data['name'];
    $timer->started_at = $data['started_at'];
    $timer->stopped_at = $data['stopped_at'];
    $start = Carbon::createFromFormat('Y-m-d H:s:i', $timer->started_at);
    $end = Carbon::createFromFormat('Y-m-d H:s:i', $timer->stopped_at);
    $timer->duration = $end->diffInSeconds($start);
    $timer->retag($data['tags']);
    $timer->save();
    return $timer;
  }

  public static function byDay($start, $end, array $userIds)
  {
    $userIds = $userIds ?? [auth()->user()->id];
    $times = Timer::select(
      [
        \DB::raw("DATE(started_at) AS timer_date"), 
        \DB::raw("SUM(duration) AS duration")
      ]
    )
      ->where('started_at', '>=', $start)
      ->where('stopped_at', '<=', $end)
      ->whereNotNull('stopped_at')
      ->whereIn('user_id', $userIds)
      ->groupBy(\DB::raw("DATE(started_at)"))
      ->get();
    return $times;
  }

  public static function byDate($start, $end, array $userIds)
  {
    $userIds = $userIds ?? [auth()->user()->id];
    $timers = Timer::select(
      [
        \DB::raw("tasks.name AS task_name"),
        \DB::raw("timers.name AS timer_name"),
        "started_at",
        "stopped_at", 
        "duration",
      ]
    )
      ->join('tasks', 'tasks.id', '=', 'timers.task_id')
      ->where('started_at', '>=', $start . " 00:00:00")
      ->where('stopped_at', '<=', $end . " 23:59:59")
      ->whereNotNull('stopped_at')
      ->whereIn('timers.user_id', $userIds)
      ->get();
    return $timers;
  }
}
