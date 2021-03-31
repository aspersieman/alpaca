<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Group;
use App\Repositories\GroupRepository;
use Illuminate\Http\Request;

class GroupController extends Controller
{
  /**
   * @var GroupRepository
   */
  private $groupRepository;

  /**
   * GroupController constructor.
   * @param GroupRepository $groupRepository
   */
  public function __construct(GroupRepository $groupRepository)
  {
    $this->groupRepository = $groupRepository;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = $this->groupRepository->index(request()->all());

    return $this->sendResponseOk($data,"Get groups ok.");
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validate = validator($request->all(),[
      'name' => 'required',
      'permissions' => 'array',
    ]);

    if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

    $group = $this->groupRepository->create($request->all());

    return $this->sendResponseOk($group, "Created.");
  }

  /**
   * Display the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $group = $this->groupRepository->find($id);

    if(!$group) return $this->sendResponseNotFound();

    return $this->sendResponseOk($group);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validate = validator($request->all(),[
      'name' => 'required',
      'permissions' => 'array',
    ]);

    if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

    $updated = $this->groupRepository->update($id, $request->all());

    if(!$updated) return $this->sendResponseBadRequest("Failed update.");

    return $this->sendResponseUpdated($group);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    /** @var Group $group */
    $group = $this->groupRepository->find($id);

    if(!$group) return $this->sendResponseNotFound();

    // prevent delete of super user
    if($group->name == Group::SUPER_USER_GROUP_NAME) return $this->sendResponseBadRequest("Cannot delete group.");

    // detach all users first
    $group->users()->detach();

    try {
      $this->groupRepository->delete($id);
    } catch (\Exception $e) {
      \Log::error($e->getMessage());
      return $this->sendResponseBadRequest("Failed to delete");
    }

    return $this->sendResponseDeleted();
  }
}
