<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Models\Note;

class NoteController extends FrontController
{
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
}
