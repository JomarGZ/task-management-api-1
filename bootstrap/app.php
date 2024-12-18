<?php

use App\Http\Middleware\AlwaysAcceptJson;
use App\Utilities\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix:'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prependToGroup('api', AlwaysAcceptJson::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function(NotFoundHttpException $e) {
            if (request()->is('api/*') && $e->getPrevious() instanceof ModelNotFoundException) {
                info('Not Found error: '. $e->getMessage());
                return ApiResponse::error(
                    'The requested data is not found in the database or not belong to the tenant',
                    'Requested resource could not be found'
                );
            }
        });
        $exceptions->renderable(function(AccessDeniedHttpException $e) {
           return ApiResponse::error(
            'You are not authorized to perform this action.',
            $e->getMessage(),
            Response::HTTP_FORBIDDEN
           );
        });
    })->create();
