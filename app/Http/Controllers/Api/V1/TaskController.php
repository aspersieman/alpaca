<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Filters\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;

class TaskController extends Controller
{
  use \Conner\Tagging\Taggable;

  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
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

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\SaveTaskRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SaveTaskRequest $request)
  {
    $request->merge(['user_id' => auth()->user()->id]);
    $task = Task::create($request->all());
    $response = $task ? array_merge($task->toArray(), ['timers' => []]) : false;
    return $this->sendResponseOk(
      $response,
      "Task created"
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
    $task = Task::find($id);

    if(!$task) return $this->sendResponseNotFound();

    return $this->sendResponseOk(
      $task,
      "Get task ok"
    );
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateTaskRequest  $request
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateTaskRequest $request, $id)
  {
    $this->authorize('update', $task);
    $request->merge(['user_id' => auth()->user()->id]);
    $task = Task::put($task->id, $request->all())->toArray();
    return $this->sendResponseOk($task, "Update task ok");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $this->authorize('delete', $task);
    Task::destroy($task->id);
    return $this->sendResponseOk([], "Task deleted");
  }
}
