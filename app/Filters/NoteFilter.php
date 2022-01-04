<?php

namespace App\Filters;

class NoteFilter extends Filters
{
  protected $filters = ['name', 'createdFrom', 'createdTo', 'userId'];

  protected function name($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('name', 'like', "%{$value}%");
  }

  protected function createdFrom($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('created_at', '>=', $value);
  }

  protected function createdTo($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->where('created_at', '<=', $value);
  }

  protected function userd($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->whereIn('user_id', split(",", $value));
  }
}
