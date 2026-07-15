<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api([
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'user.middleware' => UserMiddleware::class,
            'staff.middleware' => StaffMiddleware::class,
            'admin.middleware' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            $modelClass = class_basename($e->getModel());
            return response()->json([
                'message' => "{$modelClass} not found"
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => 'Resource not found.'
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'You are not authorized to perform this action.',
                ], Response::HTTP_FORBIDDEN);
            }
        });
    })
    ->create();
