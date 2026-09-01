<?php
declare(strict_types=1);

use App\Enums\HttpStatus;
use App\Http\Responses\Api\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => '', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(ThrottleRequestsException $e, Request $request) {
            if($request->expectsJson()) {
                return new ApiResponse(
                    status: HttpStatus::TOO_MANY_REQUESTS,
                    message: __('response.tooManyAttempts'),
                )->withHeaders($e->getHeaders());
            }
        });

        $exceptions->render(function(NotFoundHttpException $e, Request $request) {
            if($request->expectsJson()) {
                return new ApiResponse(
                    status: HttpStatus::NOT_FOUND,
                    message: __('response.notFound'),
                );
            }
        });
    })->create();
