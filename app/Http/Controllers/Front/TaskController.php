<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
    $tasks = Task::mine()
      ->with(['timers.tagged', 'project'])
      ->orderBy('order', 'asc')
      ->get()
      ->toArray();
    return $this->sendResponseOk(
      $tasks,
      "Get tasks (".count($tasks).") ok"
    );
  }

  public function current()
  {
    $tasks = Task::mine()
      ->with(['timers.tagged', 'project', 'notes'])
      ->whereRaw(
        '
        (
            tasks.status IN ("ongoing", "overdue")
            OR (
              DATE(tasks.updated_at) = CURDATE()
            )
        )
        AND tasks.deleted_at IS NULL
        '
      )
      ->orderBy('order', 'asc')
      ->get()
      ->toArray();
    return $this->sendResponseOk(
      $tasks,
      "Get current tasks (".count($tasks).") ok"
    );
  }

  public function store(Request $request)
  {
    // returns validated data as array
    $data = $request->validate([
      'name' => 'required|between:2,20',
      'status' => 'required|in:'.join(',', Task::STATUSES),
      'due' => 'date'
    ]);

    // merge with the current user ID
    $data = array_merge($data, [
      'user_id' => auth()->user()->id
    ]);

    $task = Task::create($data);
    $response = $task ? array_merge($task->toArray(), ['timers' => []]) : false;
    return $this->sendResponseOk(
      $response,
      "Task created"
    );
  }

  public function delete(Request $request, int $id)
  {
    Task::destroy($id);
    return $this->sendResponseOk([], "Task deleted");
  }

  public function status(Request $request, int $id)
  {
    $data = $request->validate([
      'status' => 'required|in:'.join(',', Task::STATUSES),
    ]);
    Task::status($id, $request->status);
    return $this->sendResponseOk([], "Update task status ok");
  }

  public function name(Request $request, int $id)
  {
    $data = $request->validate([
      'name' => 'required|between:2,100',
    ]);
    Task::name($id, $request->name);
    return $this->sendResponseOk([], "Update task name ok");
  }

  public function order(Request $request)
  {
    $updated = Task::order($request->tasks);
    return $this->sendResponseOk(["updated" => $updated], "Update task order ok");
  }

  public function update(Request $request, int $id)
  {
    $data = $request->validate([
      'name' => 'required|between:2,100',
      'status' => 'required|in:'.join(',', Task::STATUSES),
      'due' => 'date'
    ]);
    return $this->sendResponseOk(Task::put($id, $data), "Update task ok");
  }
}
