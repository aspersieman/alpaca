<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Task;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
  use \Conner\Tagging\Taggable;

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    return Task::mine()
      ->with(['timers', 'tagged'])
      ->orderBy('order', 'asc')
      ->get()
      ->toArray();
  }

  public function store(Request $request)
  {
    // returns validated data as array
    $data = $request->validate([
      'name' => 'required|between:2,20',
      'status' => 'required|in:'.join(',', Task::STATUSES),
      'due' => 'date'
    ]);
    $tags = $request['tags'];

    // merge with the current user ID
    $data = array_merge($data, [
      'user_id' => auth()->user()->id
    ]);

    $task = Task::create($data);
    if (!empty($tags)) {
      $task->tag(array_column($tags, 'text'));
      $task->tags = $task->tags ? $task->tags : [];
    }
    return $task ? array_merge($task->toArray(), ['timers' => [], 'tags' => $tags]) : false;
  }

  public function delete(Request $request, int $id)
  {
    Task::destroy($id);
    return ["status"=>"success"];
  }

  public function status(Request $request, int $id)
  {
    $data = $request->validate([
      'status' => 'required|in:'.join(',', Task::STATUSES),
    ]);
    Task::status($id, $request->status);
    return ["status" => "success"];
  }

  public function name(Request $request, int $id)
  {
    $data = $request->validate([
      'name' => 'required|between:2,20',
    ]);
    Task::name($id, $request->name);
    return ["status" => "success"];
  }

  public function order(Request $request)
  {
    $updated = Task::order($request->tasks);
    return ["status" => "success", "updated" => $updated];
  }
}
