<?php
namespace App\Components\Picture\Repositories;


use App\Components\Core\BaseRepository;
use App\Components\Picture\Models\Picture;
use Illuminate\Support\Arr;

class PictureRepository extends BaseRepository
{
  public function __construct(Picture $model)
  {
    parent::__construct($model);
  }

  /**
   * list resource
   *
   * @param array $params
   * @return PictureRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
   */
  public function index($params)
  {
    return $this->get($params,[],function($q) use ($params)
    {
      $name = Arr::get($params,'name',null);

      if($name) $q = $q->where('name','like',"%{$name}%");

      return $q;
    });
  }
}
