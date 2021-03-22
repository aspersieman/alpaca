<?php

namespace App\Filters;

class TaskFilter extends Filters
{
    protected $filters = ['n', 's', 'f', 't', 'p', 'u'];

    protected function n($value)
    {
        return $this->builder->where('name', 'like', "%{$value}%");
    }

    protected function s($value)
    {
        return $this->builder->whereIn('status', \explode(",", $value));
    }

    protected function f($value)
    {
        return $this->builder->where('due', '>=', $value);
    }

    protected function t($value)
    {
        return $this->builder->where('due', '<=', $value);
    }

    protected function p($value)
    {
        return $this->builder->where('priority', '=', $value);
    }

    protected function u($value)
    {
        return $this->builder->whereIn('user_id', split(",", $value));
    }
}
