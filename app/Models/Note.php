<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterTrait;
use \DateTimeInterface;

class Note extends Model
{
  use FilterTrait;

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'content', 'user_id', 'task_id'];

  /**
   * {@inheritDoc}
   */
  protected $with = [];

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
   * Get associated task.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  /**
   * Get my notes
   *
   * @param  \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeMine($query)
  {
    return $query->whereUserId(auth()->user()->id);
  }

  public static function put($id, $data)
  {
    $note = (new Note())->find($id);
    $note->name = $data['name'];
    $note->content = $data['content'];
    $note->save();
    return $note;
  }
}
