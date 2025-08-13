<?php

namespace App\Utils;
// Define que esta classe pertence ao namespace App\Utils.

class Tools
{
    //método usado para padronizar respostas JSON

    public static function res(string $message, int $statusCode)
    {
        return response()->json([
            // Retorna uma resposta JSON no formato
            'message' => $message,
        ], $statusCode);
    }
}
