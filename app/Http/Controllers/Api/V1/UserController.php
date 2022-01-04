<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\User;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\SaveUserRequest;

use App\Repositories\UserRepository;

use App\Utilities\Helpers;
use App\Utilities\FileHelper;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
    $this->middleware('auth:api')->except(['showAvatar']);
    $this->userRepository = $userRepository;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = $this->userRepository->listUsers(request()->all());

    return $this->sendResponseOk($data,"List resource ok.");
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

    return $this->sendResponseCreated(
      $user,
      "Resource created."
    );
  }

  /**
   * Display the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $user = $this->userRepository->find($id);

    if(!$user) return $this->sendResponseNotFound();

    return $this->sendResponseOk(
      $user,
      "Resource found."
    );
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateUserRequest  $request
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateUserRequest $request, $id)
  {
    if($request->hasFile('avatar_image')){
      $avatarImagePath = $request->file('avatar_image')->store('avatar', 'public');
      $request->merge(['avatar' => "/images/{$avatarImagePath}"]);
    }

    $payload = $request->all();

    // if password field is present but has empty value or null value
    // we will remove it to avoid updating password with unexpected value
    if(!Helpers::hasValue($payload['password'])) unset($payload['password']);

    $updated = $this->userRepository->update($id,$payload);

    if(!$updated) return $this->sendResponseBadRequest("Failed update");

    // re-sync groups
    $groupIds = [];

    \Log::error(json_encode($request->get('groups',[])));
    if($groups = $request->get('groups',[]))
    {
      foreach ($groups as $group)
      {
        if(
          isset($group['shouldAttach']) && 
          isset($group['id']) && 
          $group['shouldAttach'])
        {
          $groupIds[] = $group['id'];
        }
      }
    }
    $user = User::find($id);
    \Log::error('ATTACHING GROUPS: '.json_encode($groupIds));
    $user->groups()->sync($groupIds);

    return $this->sendResponseUpdated(
      $user,
      "Resource updated."
    );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = User::find($id);
    // do not delete self
    if($user->id == auth()->user()->id) return $this->sendResponseForbidden();

    try {
      $this->userRepository->delete($id);
    } catch (\Exception $e) {
      \Log::error($e->getMessage());
      return $this->sendResponseBadRequest("Failed to delete");
    }

    return $this->sendResponseDeleted("Resource deleted.");
  }

  /**
   * Update the specified user's avatar image.
   *
   * @param  \App\Http\Requests\UpdateUserAvatarRequest  $request
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function updateAvatar(UpdateUserAvatarRequest $request, $id)
  {
    $user = $this->userRepository->find($id);

    if(!$user) return $this->sendResponseNotFound();

    if($request->hasFile('avatar_image')){
      $avatarImagePath = $request->file('avatar_image')->store('avatar', 'public');
      Storage::disk('public')->delete($user->avatar);
      $request->merge(['avatar' => "/{$avatarImagePath}"]);
    }

    $payload = $request->all();

    $updated = $this->userRepository->update($id,$payload);

    if(!$updated) return $this->sendResponseBadRequest("Failed update");
    $user = User::find($id);

    return $this->sendResponseUpdated(
      $user,
      "Resource updated."
    );
  }

  /**
   * @param array $data
   * @return \Intervention\Image\Image|mixed
   */
  public function showAvatar($id)
  {
    $user = $this->userRepository->find($id);

    if(!$user || !$user->avatar) return $this->sendResponseNotFound();

    // get the full path of the image record found
    $fullPath = Storage::disk('public')->path($user->avatar);

    // if image and not PSD, we will create an image instance and resize accordingly
    if(FileHelper::isImage($fullPath))
    {
      $img = Image::make($fullPath);
      return $img->response();
    }

    return $this->sendResponseNotFound();
  }
}
