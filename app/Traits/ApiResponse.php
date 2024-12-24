<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function ok($message = 'Success')
    {
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => $message
        ], Response::HTTP_OK);
    }

    public function success($message, $data = null, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => $statusCode,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
   
    public function error($message, $error = null, $statusCode = Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'status' => $statusCode,
            'error' => $error,
            'message' => $message
        ], $statusCode);
    }
}
