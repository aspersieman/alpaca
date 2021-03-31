<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\SaveUserRequest;
use App\Models\User;
use App\Utilities\Helpers;
use App\Repositories\UserRepository;

class UserController extends Controller
{
  /**
   * @var UserRepository
   */
  private $userRepository;

  /**
   * UserController constructor.
   * @param UserRepository $userRepository
   */
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
    $this->middleware('auth:api');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = $this->userRepository->listUsers(request()->all());

    return $this->sendResponseOk($data,"list users ok.");
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SaveUserRequest $request)
  {
    /** @var User $user */
    $user = $this->userRepository->create($request->all());

    if(!$user) return $this->sendResponseBadRequest("Failed create.");

    // attach to group
    if($groups = $request->get('groups',[]))
    {
      foreach ($groups as $groupId => $shouldAttach)
      {
        if($shouldAttach) $user->groups()->attach($groupId);
      }
    }

    return $this->sendResponseCreated($user);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Models\User $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    if(!$user) return $this->sendResponseNotFound();

    return $this->sendResponseOk($user);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  App\Models\User $user
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateUserRequest $request, User $user)
  {
    if($request->hasFile('avatar_image')){
      $avatarImagePath = $request->file('avatar_image')->store('avatar');
      $request->merge(['avatar' => "/images/{$avatarImagePath}"]);
    }

    $payload = $request->all();

    // if password field is present but has empty value or null value
    // we will remove it to avoid updating password with unexpected value
    if(!Helpers::hasValue($payload['password'])) unset($payload['password']);

    $updated = $this->userRepository->update($user->id,$payload);

    if(!$updated) return $this->sendResponseBadRequest("Failed update");

    // re-sync groups

    $groupIds = [];

    if($groups = $request->get('groups',[]))
    {
      foreach ($groups as $groupId => $shouldAttach)
      {
        if($shouldAttach) $groupIds[] = $groupId;
      }
    }

    $user->groups()->sync($groupIds);

    return $this->sendResponseUpdated($user);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Models\User $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    // do not delete self
    if($user->id == auth()->user()->id) return $this->sendResponseForbidden();

    try {
      $this->userRepository->delete($user->id);
    } catch (\Exception $e) {
      \Log::error($e->getMessage());
      return $this->sendResponseBadRequest("Failed to delete");
    }

    return $this->sendResponseDeleted();
  }
}
