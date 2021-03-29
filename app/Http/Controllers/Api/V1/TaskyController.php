<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Task;
use App\Filters\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Requests\SaveTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskyController extends Controller
{
  use \Conner\Tagging\Taggable;

  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(TaskFilter $filter)
  {
    $tasks = Task::filter($filter)
      ->mine()
      ->orderBy('created_at', 'asc')
      ->get();
    return $this->sendResponseOk(
      new TaskCollection($tasks),
      "Get tasks (".count($tasks).") ok"
    );
  }

  public function show(Task $task)
  {
    return $this->sendResponseOk(
      $task,
      "Get task ok"
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
      ->orderBy('created_at', 'asc')
      ->get()
      ->toArray();
    return $this->sendResponseOk(
      $tasks,
      "Get current tasks (".count($tasks).") ok"
    );
  }

  public function store(SaveTaskRequest $request)
  {
    $request->merge(['user_id' => auth()->user()->id]);
    $task = Task::create($request);
    $response = $task ? array_merge($task->toArray(), ['timers' => []]) : false;
    return $this->sendResponseOk(
      $response,
      "Task created"
    );
  }

  public function destroy(int $id)
  {
    $this->authorize('delete', $blog);
    Task::destroy($id);
    return $this->sendResponseOk([], "Task deleted");
  }

  public function update(UpdateTaskRequest $request, int $id)
  {
    $this->authorize('update', $task);
    $request->merge(['user_id' => auth()->user()->id]);
    $task = Task::put($id, $data)->toArray();
    return $this->sendResponseOk($task, "Update task ok");
  }
}
