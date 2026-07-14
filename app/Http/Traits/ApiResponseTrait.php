<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ApiResponseTrait
{
    /**
     * Send a success response.
     *
     * @param array|string $data
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    public function successResponse(array|string|object $data, string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Send an error response.
     *
     * @param array|string $message
     * @param int $status
     * @return JsonResponse
     */
    public function errorResponse(string $message, array $errors=[], int $status = 500): JsonResponse
    {
        $response = [
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($response, $status);
    }

    protected function handleException(\Throwable $e, string $action)
    {
        Log::error("Error {$action}:", [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'trace' => app()->environment('production')
                ? null
                : $e->getTraceAsString(),
        ]);

        return $this->errorResponse(
            'An unexpected error occurred. Please try again later.'
        );
    }
}
