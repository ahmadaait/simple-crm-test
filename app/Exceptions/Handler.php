<?php
// app/Exceptions/Handler.php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
            }

            // Handle other exceptions as needed
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
