<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Filters\NoteFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\NoteCollection;

use App\Models\Note;

class NoteController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(NoteFilter $filter)
  {
    $notes = Note::with('task')
      ->filter($filter)
      ->mine()
      ->orderBy('created_at', 'asc')
      ->get();
    return $this->sendResponseOk(
      new NoteCollection($notes),
      "Get notes (".count($notes).") ok"
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate(
      [
        'name' => 'required|between:3,100',
        'content' => 'required|between:3,10000',
        'task_id' => 'integer|nullable'
      ]
    );
    $data = array_merge($data, [
      'user_id' => Auth::user()->id
    ]);
    $note = Note::create($data);
    $response = $note ? $note->toArray() : false;

    return $this->sendResponseOk($note->find($note->id), "Created.");
  }

  public function delete(Request $request, int $id)
  {
    Note::destroy($id);
    return $this->sendResponseOk([], "Note deleted");
  }

  public function update(Request $request, int $id)
  {
    $data = $request->validate([
      'name' => 'required|between:3,100',
      'content' => 'required'
    ]);
    return $this->sendResponseOk(Note::put($id, $data), "Note updated ok");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //$this->authorize('delete', $task);
    Note::destroy($id);
    return $this->sendResponseOk([], "Note deleted");
  }
}
