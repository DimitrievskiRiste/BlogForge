<?php

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
        $middleware->append(ApiMiddleware::class);
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\AuthorizeAPI::class, // Add alias
            'scope.attachments' => \App\Http\Middleware\AttachmentMiddleware::class,
            'scope.add_category' => \App\Http\Middleware\CategoryMiddleware::class,
            'scope.edit_category' => \App\Http\Middleware\EditCategoryMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
