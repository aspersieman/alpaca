<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PingController extends Controller
{
    public function index()
    {
        return response(["message" => "pong"]);
    }
}
