<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Project extends Model
{
  use SoftDeletes;

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'created_by_user_id'];

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
   * Get associated tasks.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function tasks()
  {
    return $this->hasMany(Task::class);
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

  public static function put($id, $data)
  {
    $project = Project::where('id', $id)->first();
    $project->name = $data['name'];
    $project->due = $data['due'];
    $project->status = $data['status'];
    $project->save();
    return $project;
  }
}
