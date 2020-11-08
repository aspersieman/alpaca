<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
  use SoftDeletes;

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'status', 'due', 'user_id', 'project_id'];

  /**
   * {@inheritDoc}
   */
  protected $with = ['user', 'project'];

  const STATUSES = [
    'complete',
    'ongoing',
    'overdue'
  ];

  /**
   * Get associated user.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get associated project.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  /**
   * Get associated timers.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function timers()
  {
    return $this->hasMany(Timer::class);
  }

  /**
   * Get my tasks
   *
   * @param  \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeMine($query)
  {
    return $query->whereUserId(auth()->user()->id);
  }

  /**
   * Update status
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public static function status($id, $status)
  {
    return Task::withTrashed()
      ->where('id', $id)
      ->update(['status' => $status]);
  }

  /**
   * Update name
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public static function name($id, $name)
  {
    return Task::withTrashed()
      ->where('id', $id)
      ->update(['name' => $name]);
  }

  /**
   * Update order
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public static function order($tasks)
  {
    $updated = [];
    foreach($tasks as $task) {
      $count = Task::where('id', $task['id'])
        ->update(['order' => $task['order']]);
      if ($count > 0) $updated[$task['id']] = $task['order'];
    }
    return $updated;
  }

  public static function put($id, $data)
  {
    $task = Task::withTrashed()
      ->find($id);
    $task->name = $data['name'];
    $task->due = $data['due'];
    $task->status = $data['status'];
    $task->save();
    return $task;
  }
}
