<?php

namespace App\Utils;

class Tools
{
    public static function res(string $message, int $statusCode)
    {
        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}
