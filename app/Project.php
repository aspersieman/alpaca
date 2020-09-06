<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
  use SoftDeletes;

  /**
   * {@inheritDoc}
   */
  protected $fillable = ['name', 'created_by_user_id'];
}
