<?php

namespace App\Filters;

class TaskFilter extends Filters
{
  protected $filters = ['name', 'status', 'dueFrom', 'dueTo', 'priority', 'userId', 'default'];

  protected function default($value)
  {
    if (empty($value)) {
      return $this->builder;
    }
    return $this->builder->whereRaw(
      '
        (
            tasks.status IN ("inprogress", "todo")
            OR (
              DATE(tasks.updated_at) = CURDATE()
            )
        )
        AND tasks.deleted_at IS NULL
      '
      );
    }

    protected function name($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->where('name', 'like', "%{$value}%");
    }

    protected function status($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->whereIn('status', \explode(",", $value));
    }

    protected function dueFrom($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->where('due', '>=', $value);
    }

    protected function dueTo($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->where('due', '<=', $value);
    }

    protected function priority($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->where('priority', '=', $value);
    }

    protected function user($value)
    {
      if (empty($value)) {
        return $this->builder;
      }
      return $this->builder->whereIn('user_id', split(",", $value));
    }
}
