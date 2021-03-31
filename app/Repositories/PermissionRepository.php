<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Utilities\Helpers;
use App\Models\Permission;
use Illuminate\Support\Arr;

class PermissionRepository extends BaseRepository
{
  public function __construct(Permission $model)
  {
    parent::__construct($model);
  }

  /**
   * index items
   *
   * @param array $params
   * @return \Illuminate\Database\Eloquent\Model[]|\Illuminate\Support\Collection
   */
  public function index($params)
  {
    return $this->get($params,[],function($q) use ($params)
    {
      $title = Arr::get($params,'title','');

      $q->where('title','like',"%{$title}%");

      return $q;
    });
  }
}
