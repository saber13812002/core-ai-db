<?php

use App\Exceptions\FileDownloadException;
use App\Exceptions\FileUploadException;
use App\Exceptions\MetadataSchemaNotFoundException;
use App\Exceptions\MetadataValidationException;
use App\Http\Middleware\EnsureApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            EnsureApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (FileUploadException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => ['code' => $e->errorCode, 'message' => $e->getMessage()],
                ], $e->httpStatus);
            }

            return null;
        });

        $exceptions->render(function (FileDownloadException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => ['code' => 'file_unavailable', 'message' => $e->getMessage()],
                ], $e->httpStatus);
            }

            return null;
        });

        $exceptions->render(function (MetadataSchemaNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => ['code' => 'metadata_schema_not_found', 'message' => $e->getMessage()],
                ], 422);
            }

            return null;
        });

        $exceptions->render(function (MetadataValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => ['code' => 'metadata_invalid', 'message' => $e->getMessage()],
                    'errors' => $e->errors,
                ], 422);
            }

            return null;
        });
    })->create();
