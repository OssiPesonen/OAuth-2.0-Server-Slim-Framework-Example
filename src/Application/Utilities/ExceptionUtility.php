<?php

namespace App\Application\Utilities;

class ExceptionUtility {
    public static function toArray($e): array {
        $arr = [];

        if($e instanceof \Exception) {
            $arr = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode()
            ];
        }

        return $arr;
    }
}
