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
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix:'api/v1'
    )
    ->withBroadcasting(base_path('routes/channels.php'), attributes: ['middleware' => ['api', 'auth:sanctum']])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prependToGroup('api', AlwaysAcceptJson::class);
        $middleware->group('broadcasting', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
        ]);
          
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function(NotFoundHttpException $e) {
            if (
                request()->is('api/*') &&
                request()->wantsJson() && // More robust check
                ($e instanceof ModelNotFoundException || $e->getPrevious() instanceof ModelNotFoundException)
            ) {
                return response()->json([
                    'errors' => [
                        'title' => 'Not Found',
                        'status' => Response::HTTP_NOT_FOUND
                    ]
                    ], Response::HTTP_NOT_FOUND);
            }
        });
        $exceptions->renderable(function(AccessDeniedHttpException $e) {
            return response()->json([
                'title' => $e->getMessage(),
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        });
    })->create();
