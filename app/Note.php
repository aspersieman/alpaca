<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'content', 'user_id', 'task_id'];

  /**
   * {@inheritDoc}
   */
  protected $with = [];

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

  public static function put($id, $data)
  {
    $note = (new Note())->find($id);
    $note->name = $data['name'];
    $note->content = $data['content'];
    $note->save();
    return $note;
  }
}
