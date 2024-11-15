<?php

use App\Http\Resources\BaseResponseResource;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 403) {
                return new BaseResponseResource(false, 'You are not authorized to access this resource.', null, 403);
            } else if ($response->getStatusCode() === 404) {
                return new BaseResponseResource(false, 'Resource not found.', null, 404);
            } else if ($response->getStatusCode() === 405) {
                return new BaseResponseResource(false, 'Method not allowed.', null, 405);
            } else if ($response->getStatusCode() === 500) {
                return new BaseResponseResource(false, 'Internal server error.', null, 500);
            }

            return $response;
        });
    })->create();
