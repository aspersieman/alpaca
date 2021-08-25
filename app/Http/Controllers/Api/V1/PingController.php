<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\TestJob;

class PingController extends Controller
{
    public function index()
    {
      TestJob::dispatch();
      return response(["message" => "pong"]);
    }
}
