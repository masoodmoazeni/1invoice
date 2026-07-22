<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as Controller;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;
}
