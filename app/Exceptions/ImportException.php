<?php

namespace App\Exceptions;

use Exception;

class ImportException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? _('front.import.error'));
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 500);
    }
}
