<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterTrait;
use \DateTimeInterface;

class Task extends Model
{
  use SoftDeletes, FilterTrait;

  protected $table = 'tasks';

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'status', 'due', 'user_id', 'project_id', 'priority', 'completed_at'];

  /**
   * {@inheritDoc}
   */
  protected $with = [];

  const STATUSES = [
    'complete',
    'inprogress',
    'todo'
  ];

  const PRIORITIES = [
    0, // none
    1, // high
    2, // medium
    3 // low
  ];

  /**
   * Prepare a date for array / JSON serialization.
   *
   * @param  \DateTimeInterface  $date
   * @return string
   */
  protected function serializeDate(DateTimeInterface $date)
  {
      return $date->format('Y-m-d H:i:s');
  }

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
   * Get associated notes.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function notes()
  {
    return $this->hasMany(Note::class);
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
    $completedAt = null;
    if ($status === 'complete') {
      $completedAt = (new \DateTime('NOW'))->format("Y-m-d H:i:s");
    }
    return Task::withTrashed()
      ->where('id', $id)
      ->update([
        'status' => $status,
        'completed_at' => $completedAt,
      ]);
  }

  public static function put($id, $data)
  {
    $task = Task::withTrashed()
      ->find($id);
    $task->name = $data['name'];
    $task->due = $data['due'];
    $task->status = $data['status'];
    if ($data['status'] === 'complete') {
      $task->completed_at = (new \DateTime('NOW'))->format("Y-m-d H:i:s");
    } else {
      $task->completed_at = null;
    }
    $task->priority = $data['priority'];
    $task->save();
    return $task;
  }
}
