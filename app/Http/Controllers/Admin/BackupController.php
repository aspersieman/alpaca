<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

use App\Utilities\GoogleDriveHelper;

class BackupController extends Controller{
  public function fetchVerificateCode(Request $request){

    $code = $request->code;

    $isSuccess = (new GoogleDriveHelper())->prepareAuthCode($code);

    if($isSuccess){
      echo "You have logged successfully.";
      return redirect('/admin/backups/');
    } else {
      echo "Error occurred when during login.";
    }
  }
}
