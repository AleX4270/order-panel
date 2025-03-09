<?php
declare(strict_types=1);

namespace App\Http\Responses\Api;

use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse {
    public function __construct(
        mixed $data = null,
        HttpStatus $statusCode = HttpStatus::OK,
        ?string $message = ''
    ) {
        $response = [
            'data' => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        parent::__construct($response, $statusCode->value);
    }
}
