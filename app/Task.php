<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Conner\Tagging\Taggable;

class Task extends Model
{
  use \Conner\Tagging\Taggable;
  use SoftDeletes;

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'status', 'due', 'tags', 'user_id'];

  /**
   * {@inheritDoc}
   */
  protected $with = ['user'];

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
    return Task::where('id', $id)
      ->update(['status' => $status]);
  }

  /**
   * Update name
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public static function name($id, $name)
  {
    return Task::where('id', $id)
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
}
