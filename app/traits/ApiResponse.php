<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Exception;

trait ApiResponse
{
    /**
     * Helper function for successful JSON response
     */
    protected function sendResponse($data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Helper function for error JSON response
     */
    protected function sendError(string $message, int $status = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => null
        ], $status);
    }

    /**
     * Handle exception and return JSON response
     */
    protected function handleException(Exception $e, int $status = 500): JsonResponse
    {
        return $this->sendError(
            'Something went wrong!',
            $status,
            env('APP_DEBUG') ? $e->getMessage() : null
        );
    }
}