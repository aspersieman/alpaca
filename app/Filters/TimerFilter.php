<?php

namespace App\Filters;

class TimerFilter extends Filters
{
  protected $filters = ['name', 'from', 'to', 'user', 'task'];

  protected function name($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('name', 'like', "%{$value}%");
  }

  protected function from($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('started_at', '>=', $value);
  }

  protected function to($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('started_at', '<=', $value);
  }

  protected function user($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->whereIn('user_id', split(",", $value));
  }

  protected function task($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->whereIn('task_id', split(",", $value));
  }
}
