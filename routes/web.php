<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * API Routes
 */
Route::middleware(\App\Http\Middleware\ApiMiddleware::class)->group(function(){
    /**
     * Route for handling login with JWT token
     */
   Route::controller(\App\Http\Controllers\LoginController::class)->group(function(){
       Route::post("/login", 'login');
   });
});
