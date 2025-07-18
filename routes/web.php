<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserGroupsController;
use App\Http\Controllers\WebsiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * API Routes
 */
Route::middleware(['api'])->prefix("api")->withoutMiddleware(['web'])->group(function(){
    /**
     * Route for handling login with JWT token
     */
   Route::controller(LoginController::class)->group(function(){
       Route::post("/login", 'login');
   });
   // Authorized API Routes. Required JWT Token for access
    Route::middleware(['auth.api','scope.attachments'])->group(function(){
        Route::controller(AttachmentsController::class)->group(function(){
            Route::post("/attachments/upload", 'upload');
        });
    });

    // Routes for Categories. Currently implemented list categories and adding new category
    Route::controller(CategoriesController::class)->group(function(){
        Route::get("/categories", "list");
        // scope add_category
        Route::post("/category/add", "add")->middleware(['auth.api','scope.add_category']);

        Route::get('/category_info/{slug}','get');
        // Scope edit_category
        Route::post("/edit_category", 'edit')->middleware(['auth.api','scope.edit_category']);
        // Scope remove_category
        Route::post('/remove_category', 'delete')->middleware(['auth.api', 'scope.remove_category']);
    });
    // Routes for user groups
    Route::controller(UserGroupsController::class)->group(function(){
        Route::get('/groups/get', 'list')->middleware(['auth.api','scope.can_see_user_groups']);
        Route::post('/groups/add', 'add')->middleware(['auth.api','scope.can_add_user_groups']);
        Route::post('/groups/edit', 'edit')->middleware(['auth.api','scope.can_edit_user_groups']);
        Route::post('/groups/remove', 'delete')->middleware(['auth.api','scope.can_remove_user_groups']);
    });
    // Route for website settings
    Route::controller(WebsiteSettingsController::class)->group(function(){
       Route::get('/settings','list')->middleware(['auth.api','scope.can_change_settings']);
       Route::post('/settings/save','save')->middleware(['auth.api','scope.can_change_settings']);
    });
    // Routes for articles
    Route::controller(ArticlesController::class)->group(function(){
        Route::get('/articles','list');
        Route::post('/article/add', 'add')->middleware(['auth.api','scope.can_add_articles']);
    });
});
