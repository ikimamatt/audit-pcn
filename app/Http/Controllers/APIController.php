<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class APIController extends Controller
{
    /**
     * Standard success JSON response for API
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendSuccess($data, string $message, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Standard error JSON response for API
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }
}
