<?php

namespace App\Filters;

class NoteFilter extends Filters
{
    protected $filters = ['n', 'f', 't', 'u'];

    protected function n($value)
    {
        return $this->builder->where('name', 'like', "%{$value}%");
    }

    protected function f($value)
    {
        return $this->builder->where('created_at', '>=', $value);
    }

    protected function t($value)
    {
        return $this->builder->where('created_at', '<=', $value);
    }

    protected function u($value)
    {
        return $this->builder->whereIn('user_id', split(",", $value));
    }
}
