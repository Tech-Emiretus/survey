<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    public static function success(array $data = [], ?string $message = null, int $status = JsonResponse::HTTP_OK): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message, int $status = JsonResponse::HTTP_BAD_REQUEST): self
    {
        return new self([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
