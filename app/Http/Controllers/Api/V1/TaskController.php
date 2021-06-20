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
    $tasks = Task::with(['user', 'project', 'notes', 'timers.tagged'])
      ->filter($filter)
      ->mine()
      ->orderBy('created_at', 'asc')
      ->get();
    return $this->sendResponseOk(
      new TaskCollection($tasks),
      "List resource ok."
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
    return $this->sendResponseCreated(
      $response,
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
    $task = Task::with(['user', 'project', 'notes', 'timers.tagged'])
      ->find($id);

    if(!$task) return $this->sendResponseNotFound();

    return $this->sendResponseOk(
      $task,
      "Resource found."
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
    $task = Task::put($id, $request->all())->toArray();
    return $this->sendResponseOk($task, "Resource updated.");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Task::destroy($id);
    return $this->sendResponseDeleted("Resource deleted.");
  }
}
