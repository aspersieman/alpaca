<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utilities\GoogleDriveHelper;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BackupController extends Controller{

  public function index( Request $request ){

    $token_info = ( new GoogleDriveHelper() )->prepareToken();

    $info = [
      'is_authenticated' => ( $token_info['is_token_valid'] )? true : false,
      'drive_login_url' => $token_info['auth_url'],
    ];

    return view( 'backups.default', $info );
  }

  public function fetchVerificateCode( Request $request ){

    $code = $request->code;

    $is_success = ( new GoogleDriveHelper() )->prepareAuthCode( $code );

    if( $is_success ){
      echo "You have logged successfully.";
    } else {
      echo "You have error while login.";
    }

  }

  public function zipApplicationFiles( ){

    // Get real path for our folder
    $rootPath = realpath('.');

    // Initialize archive object
    $zip_file = public_path('zip_files_backups/files.zip');
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($rootPath),
      RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file)
    {
      // Skip directories (they would be added automatically)
      if (!$file->isDir())
      {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
      }
    }

    // Zip archive will be created only after closing object
    $zip->close();

    return $zip_file;
  }


  public function sendToDrive(){
    $googleDriveHelper = new GoogleDriveHelper();
    $fileList = $googleDriveHelper->getDriveFiles();
    $fileList = !empty($fileList) ? array_column($fileList['files'], 'name') : [];
    print_r('Files on Google Drive');
    print_r('<br>');
    print_r($fileList);
    print_r('<br>');
    $res = [];
    $iti = new \RecursiveDirectoryIterator(database_path('backups/'));
    print_r('Uploading...');
    print_r('<br>');
    foreach(new \RecursiveIteratorIterator($iti) as $file){
      if(!in_array(basename($file), $fileList) && strpos($file , 'lamoid.sql.bz2') !== false){
        print_r(basename($file));
        print_r('<br>');
        $res = $googleDriveHelper->uploadToDrive(basename($file), dirname($file));
      }
    }
    print_r('<br>');
    print_r('Done');
    print_r('<br>');
    print_r('Results from Google Drive:');
    print_r('<br>');
    print_r($res);
    print_r('<br>');
  }
}
