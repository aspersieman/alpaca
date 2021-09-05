<?php
namespace App\Utilities;

use Exception;
use Google_Service_Drive;
use Google\Client as Google_Client;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_FileList;

class GoogleDriveHelper {

  protected $drive;
  protected $credentials;

  public function __construct()
  {
    $this->initialize();
    $this->prepareToken();
  }

  public function initialize(){
    $this->credentials = public_path('secret/credentials.json');

    $this->drive = new Google_Client();
    $this->drive->setApplicationName('Lamoid Backups');
    $this->drive->setScopes(Google_Service_Drive::DRIVE);
    $this->drive->setAuthConfig($this->credentials);
    $this->drive->setAccessType('offline');
    $this->drive->setPrompt('select_account consent');
  }

  // here we should take care of access token
  public function getTokenFile(){
    return public_path('secret/token.json');
  }

  public function prepareToken(){

    $info = [
      'is_token_valid' => false,
      'auth_url' => null,
    ];

    $token_file = $this->getTokenFile();
    if (file_exists($token_file)) {
      $accessToken = json_decode(file_get_contents($token_file), true);
      $this->drive->setAccessToken($accessToken);
      $info['is_token_valid'] = true;
    }

    if ($this->drive->isAccessTokenExpired()) {
      if ($this->drive->getRefreshToken()) {
        $this->drive->fetchAccessTokenWithRefreshToken($this->drive->getRefreshToken());
        $info['is_token_valid'] = true;
      } else {
        $info['is_token_valid'] = false;
        $info['auth_url'] = $this->drive->createAuthUrl();
      }
    }

    return $info;
  }

  public function prepareAuthCode($code){
    $isSuccess = false;
    try {
      $code = trim($code);

      $accessToken = $this->drive->fetchAccessTokenWithAuthCode($code);
      $this->drive->setAccessToken($accessToken);

      // Check to see if there was an error.
      if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
      }

      // Save the token to a file.
      $tokenPath = $this->getTokenFile();
      if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
      }
      file_put_contents($tokenPath, json_encode($this->drive->getAccessToken()));
      $isSuccess = true;
    } catch (\Exception $th) {
      $isSuccess = false;
      throw $th;
    }

    return $isSuccess;

  }

  public function getDriveFiles() {
    $service = new Google_Service_Drive($this->drive);
    $file = new Google_Service_Drive_DriveFile();
    $folderId = $this->createFolder(env('APP_NAME').'/backups');
    $optParams = array(
      'pageSize' => 10,
      'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType,parents)",
      'q' => "'".$folderId."' in parents"
    );
    return $service->files->listFiles($optParams);
  }

  public function uploadToDrive($fileName, $filePath){

    $service = new Google_Service_Drive($this->drive);
    $file = new Google_Service_Drive_DriveFile();
    $filePath = $filePath."/".$fileName;
    $folderId = $this->createFolder(env('APP_NAME').'/backups');

    $file->setName($fileName);

    if(!empty($folderId)){
      $file->setParents([ $folderId ]);
    }

    $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents($filePath),
        'mimeType' => 'application/octet-stream',
     )
   );

    $isSuccess = false;

    if(isset($result['name']) && !empty($result['name'])){
      $isSuccess = true;
    }

    return $isSuccess;
  }

  public function createFolder($folder_name, $parent_folderId=null){

    $folder_list = $this->checkFolderExists($folder_name);

    // if folder does not exists
    if(count($folder_list) == 0){
      $service = new Google_Service_Drive($this->drive);
      $folder = new Google_Service_Drive_DriveFile();

      $folder->setName($folder_name);
      $folder->setMimeType('application/vnd.google-apps.folder');
      if(!empty($parent_folderId)){
        $folder->setParents([ $parent_folderId ]);
      }

      $result = $service->files->create($folder);

      $folderId = null;

      if(isset($result['id']) && !empty($result['id'])){
        $folderId = $result['id'];
      }

      return $folderId;
    }

    return $folder_list[0]['id'];

  }

  public function checkFolderExists($folder_name){

    $service = new Google_Service_Drive($this->drive);

    $parameters['q'] = "mimeType='application/vnd.google-apps.folder' and name='$folder_name' and trashed=false";
    $files = $service->files->listFiles($parameters);

    $op = [];
    foreach($files as $k => $file){
      $op[] = $file;
    }

    return $op;
  }

  public function sendToDrive() {
    $fileList = $this->getDriveFiles();
    $fileList = !empty($fileList) ? array_column($fileList['files'], 'name') : [];
    $res = [];
    $iti = new \RecursiveDirectoryIterator(database_path('backups/'));
    foreach(new \RecursiveIteratorIterator($iti) as $file){
      if(!in_array(basename($file), $fileList) && strpos($file , '.sql.bz2') !== false){
        $res[basename($file)] = $this->uploadToDrive(basename($file), dirname($file));
      }
    }

    return $res;
  }

}
