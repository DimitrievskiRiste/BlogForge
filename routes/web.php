<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * API Routes
 */
Route::middleware(\App\Http\Middleware\ApiMiddleware::class)->prefix("api")->withoutMiddleware([\App\Http\Middleware\AuthorizeAPI::class])->group(function(){
    /**
     * Route for handling login with JWT token
     */
   Route::controller(\App\Http\Controllers\LoginController::class)->group(function(){
       Route::post("/login", 'login');
   });
   // Authorized API Routes. Required JWT Token for access
   Route::middleware("auth.api")->group(function() {
       // AttachmentsController routes
       Route::controller(\App\Http\Controllers\AttachmentsController::class)->group(function(){
           Route::post("/attachments/upload", 'upload');
       });
       // Route for Categories. Currently implemented list categories and adding new category
       Route::controller(\App\Http\Controllers\CategoriesController::class)->group(function(){
           Route::get("/categories", "actionList");
           Route::post("/category/add", "actionAdd");
       });
   });

});
