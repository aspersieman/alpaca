<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Filters\NoteFilter;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\NoteCollection;
use App\Http\Requests\SaveNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

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
      "List resource ok."
    );
  }

  public function store(SaveNoteRequest $request)
  {
    $request->merge(['user_id' => auth()->user()->id]);
    $note = Note::create($request->all());
    $response = $note ? $note->toArray() : false;

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
    $note = Note::with(['user'])
      ->find($id);

    if(!$note) return $this->sendResponseNotFound();

    return $this->sendResponseOk(
      $note,
      "Resource found."
    );
  }

  public function update(UpdateNoteRequest $request, int $id)
  {
    return $this->sendResponseOk(Note::put($id, $request), "Resource updated.");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Note::destroy($id);
    return $this->sendResponseDeleted("Resource deleted.");
  }
}
