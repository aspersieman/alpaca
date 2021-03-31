<?php

namespace App\Traits;

use App\Filters\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

trait FilterTrait
{
    public function scopeFilter(Builder $query, FilterInterface $filters): Builder
    {
        return $filters->apply($query);
    }

}
