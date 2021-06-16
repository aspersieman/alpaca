<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Repositories\FileRepository;
use App\Services\FileService;
use Illuminate\Http\Request;
use Auth;
use League\Flysystem\FileNotFoundException;

class FileController extends Controller
{
  /**
   * @var FileRepository
   */
  private $fileRepository;
  /**
   * @var FileService
   */
  private $fileService;

  /**
   * FileController constructor.
   * @param FileRepository $fileRepository
   * @param FileService $fileService
   */
  public function __construct(FileRepository $fileRepository, FileService $fileService)
  {
    $this->middleware('auth:api')->except(['index', 'show', 'fileDownload', 'filePreview']);
    $this->fileRepository = $fileRepository;
    $this->fileService = $fileService;
  }

  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $results = $this->fileRepository->index($request->all());

    return $this->sendResponseOk($results);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data  = $request->all();
    $files = $request->file('file');
    $error = false;
    $errorMessage = '';
    $fileRecord = null;

    foreach ($files as $file)
    {
      try {
        $fileData = $this->fileService->upload($file);
      } catch (FileNotFoundException $e) {
        $error = true;
        $errorMessage = $e->getMessage();
        break;
      }

      if(!$fileData)
      {
        $error = true;
        $errorMessage = $this->fileService->getErrors()->first();
        break;
      }

      /** @var File $fileRecord */
      $fileRecord = $this->fileRepository->create([
        'name' => $fileData['original_name'],
        'uploaded_by' => Auth::user()->id,
        'file_group_id' => $data['file_group_id'],
        'file_type' => $fileData['type'],
        'extension' => $fileData['ext'],
        'size' => $fileData['size'],
        'path' => $fileData['path'],
      ]);

      if(!$fileRecord)
      {
        $this->fileService->deleteFile($fileData['path']);
        $error = true;
        $errorMessage = "Failed to create record.";
        break;
      }
    }

    if($error)
    {
      return $this->sendResponseBadRequest($errorMessage);
    }

    return $this->sendResponseCreated($fileRecord);
  }

  /**
   * show file details
   *
   * @param Request $request
   * @param int $id
   */
  public function show(Request $request, $id)
  {
    $file = $this->fileRepository->find($id);

    if(!$file) return $this->sendResponseNotFound();

    return $this->sendResponseOk($file);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    return $this->sendResponseNoContent();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      $this->fileRepository->deleteRecordAndFile($id);
    } catch (\Exception $e) {
      return $this->sendResponseBadRequest("Failed to delete file.");
    }

    return $this->sendResponseDeleted();
  }

  /**
   * preview file web request
   *
   * @param Request $request
   * @param int $id
   * @return mixed
   */
  public function filePreview(Request $request,$id)
  {
    $validate = validator($request->all(),[
      'w' => 'required',
      'h' => 'required',
    ]);

    if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

    $file = $this->fileService->previewFile([
      'id' => $id,
      'w' => $request->get('w',null),
      'h' => $request->get('h',null),
      'aspect_ratio' => $request->get('aspect_ratio',true),
      'up_size' => $request->get('up_size',true),
      'action' => $request->get('action','resize'), // resize|fit
    ]);

    return $file;
  }

  /**
   * download file web request
   *
   * @param Request $request
   * @param int $id
   * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null|\Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function fileDownload(Request $request, $id)
  {
    $token = $request->get('file_token');

    $res = $this->fileService->downloadFile($id,$token);

    if($res) return $res;

    return $this->sendResponseForbidden();
  }
}
