<?php
namespace App\Utilities;

use Illuminate\Http\Response;

class ApiResponse {

    public static function success($data, $message = 'Success', $status = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    public static function error($message = 'Error', $errors = null, $status = Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

   
}