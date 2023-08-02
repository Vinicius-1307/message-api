<?php

namespace App\Builder;

use Exception;
use Illuminate\Http\JsonResponse;

final class ReturnApi
{
    /**
     * messageReturn
     *
     * @param  bool $error
     * @param  string $message
     * @param  string $developerMessage
     * @param  \Throwable $exception
     * @param  mixed $data
     * @param  int $statusHTTP
     */
    public static function messageReturn(bool $error, ?string $message, ?string $developerMessage, ?string $exception, $data, int $statusHTTP): JsonResponse
    {
        $result = [
            'error' => $error,
            'message' =>  $message,
            'developerMessage' => $developerMessage,
            'exception' => $exception,
            'data' => $data
        ];

        return response()->json($result,  $statusHTTP, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Return success JSON response
     *
     * @param  string $message
     * @param  mixed $data
     * @param  int $statusHTTP
     */
    public static function Success(?string $message, $data, ?int $statusHTTP = 200): JsonResponse
    {
        return self::messageReturn(false, $message, null, null, $data, $statusHTTP);
    }

    /**
     * Return error JSON response
     *
     * @param  string $message
     * @param  string $developerMessage
     * @param  string $exception
     * @param  int $statusHTTP
     * @param  mixed $data
     */
    public static function Error(string $message, ?string $developerMessage, ?string $exception = null, ?int $statusHTTP = 400, $data = null): JsonResponse
    {
        return self::messageReturn(true, $message, $developerMessage, $exception, $data, $statusHTTP);
    }
}
