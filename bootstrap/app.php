<?php

use App\Http\Middleware\AttachmentMiddleware;
use App\Http\Middleware\AuthorizeAPI;
use App\Http\Middleware\CategoryMiddleware;
use App\Http\Middleware\EditCategoryMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ApiMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api' => ApiMiddleware::class,
            'auth.api' => AuthorizeAPI::class, // Add alias
            'scope.attachments' => AttachmentMiddleware::class,
            'scope.add_category' => CategoryMiddleware::class,
            'scope.edit_category' => EditCategoryMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
