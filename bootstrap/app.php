<?php

use App\Http\Middleware\AttachmentMiddleware;
use App\Http\Middleware\AuthorizeAPI;
use App\Http\Middleware\CanAddArticlesMiddleware;
use App\Http\Middleware\CanAddUserGroupsMiddleware;
use App\Http\Middleware\CanEditArticlesMiddleware;
use App\Http\Middleware\CanEditUserGroupsMiddleware;
use App\Http\Middleware\CanManageSettingsMiddleware;
use App\Http\Middleware\CanRemoveArticlesMiddleware;
use App\Http\Middleware\CanRemoveUserGroupsMiddleware;
use App\Http\Middleware\CanSeeUserGroupsMiddleware;
use App\Http\Middleware\CategoryMiddleware;
use App\Http\Middleware\EditCategoryMiddleware;
use App\Http\Middleware\RemoveCategoryMiddleware;
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
            'scope.remove_category' => RemoveCategoryMiddleware::class,
            'scope.can_see_user_groups' => CanSeeUserGroupsMiddleware::class,
            'scope.can_add_user_groups' => CanAddUserGroupsMiddleware::class,
            'scope.can_edit_user_groups' => CanEditUserGroupsMiddleware::class,
            'scope.can_remove_user_groups' => CanRemoveUserGroupsMiddleware::class,
            'scope.can_change_settings' => CanManageSettingsMiddleware::class,
            'scope.can_add_articles' => CanAddArticlesMiddleware::class,
            'scope.can_edit_articles' => CanEditArticlesMiddleware::class,
            'scope.can_remove_articles' => CanRemoveArticlesMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
