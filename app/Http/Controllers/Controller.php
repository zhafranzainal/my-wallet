<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function return_api($isSuccess, $statusCode,  $message, $data, $error)
    {
        return response()->json([
            'is_success' => $isSuccess,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'errors' => $error,

        ], $statusCode);
    }
}
