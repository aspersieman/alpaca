<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Utilities\GoogleDriveHelper;

class DbBackupController extends Controller
{

  /**
   * DbBackupController constructor.
   */
  public function __construct()
  {
    $this->middleware('auth:api')->except(['sendToDrive']);
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
    if (!auth()->user()->isSuperUser()) {
      abort(403);
    }
    $path = $this->recursiveDirectorySearch(database_path('backups/'), $name);
    if (!$path || !is_file($path)) {
      return $this->sendResponseNotFound('Resource not found');
    }
    if (str_starts_with($path, database_path('backups/'))) {
      unlink($path);
      return $this->sendResponseDeleted('Resource deleted.');
    }
  }

  public function isGoogleDriveAuthenticated(){
    if (!auth()->user()->isSuperUser()) {
      abort(403);
    }
    $token_info = (new GoogleDriveHelper())->prepareToken();

    $info = [
      'is_authenticated' => ( $token_info['is_token_valid'] )? true : false,
      'drive_login_url' => $token_info['auth_url'],
      'upload_to_drive_auth_code' => env('GOOGLE_DRIVE_UPLOAD_RUN_CODE', ''),
    ];

    return $this->sendResponseOk($info, "Google Drive Authentication result.");
  }

  public function sendToDrive(Request $request){
    $requestData = $request->all();
    $GOOGLE_DRIVE_UPLOAD_RUN_CODE = $requestData['code'] ?? '';
    if (($GOOGLE_DRIVE_UPLOAD_RUN_CODE !== env('GOOGLE_DRIVE_UPLOAD_RUN_CODE', rand(0, 100)))) {
      abort(403);
    }
    $googleDriveHelper = new GoogleDriveHelper();
    return $this->sendResponseOk($googleDriveHelper->sendToDrive(), "Response from Google Drive.");
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
