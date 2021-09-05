<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DbBackupController extends Controller
{

  /**
   * DbBackupController constructor.
   */
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $requestData = $request->all();
    $name = $requestData['name'] ?? '';
    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(database_path('backups/')));

    $files = [];
    foreach ($rii as $file) {
      $filePath = $file->getPathname();
      if (!$file->isDir() && str_contains($filePath, $name)) {
        $files[] = [
          'id' => basename($filePath),
          'path' => dirname($filePath),
          'name' => basename($filePath),
          'size' => $this->formatFilesize(filesize($filePath)),
        ];
      }
    }

    return $this->sendResponseOk($files, "List resource ok.");
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\SaveTaskRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store()
  {
    if (!auth()->user()->isSuperUser()) {
      abort(403);
    }
    \Artisan::call("db:dump");
    return $this->sendResponseCreated(
      [],
      "Resource created."
    );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  string  $name
   * @return \Illuminate\Http\Response
   */
  public function destroy($name)
  {
    $path = $this->recursiveDirectorySearch(database_path('backups/'), $name);
    if (!$path || !is_file($path)) {
      return $this->sendResponseNotFound('Resource not found');
    }
    if (str_starts_with($path, database_path('backups/'))) {
      unlink($path);
      return $this->sendResponseDeleted('Resource deleted.');
    }
  }

  private function formatFilesize($bytes, $dec = 2)
  {
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
  }

  private function recursiveDirectorySearch($folder, $pattern) {
    $iti = new \RecursiveDirectoryIterator($folder);
    foreach(new \RecursiveIteratorIterator($iti) as $file){
      if(strpos($file , $pattern) !== false){
        return $file;
      }
    }
    return false;
  }
}
