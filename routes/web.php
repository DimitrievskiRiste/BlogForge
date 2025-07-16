<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LoginController;
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

    // Route for Categories. Currently implemented list categories and adding new category
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

});
