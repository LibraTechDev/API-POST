<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PostActivityController;

Route::post('/register', [AuthController::class, 'register']); //pass & pass exception
Route::post('/login', [AuthController::class, 'login']); //pass & pass exception
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum'); //pass & pass exception

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::apiResource('users', UserController::class); //pass & pass exception
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::patch('categories/restore/{id}', [CategoryController::class, 'restore']);
        Route::delete('categories/force-delete/{id}', [CategoryController::class, 'forceDelete']);
    });


    Route::group([], function () {
        Route::apiResource('tags', TagController::class);
        Route::patch('tags/restore/{id}', [TagController::class, 'restore'])
            ->middleware(EnsureUserIsAdmin::class);
        Route::delete('tags/force-delete/{id}', [TagController::class, 'forceDelete'])
            ->middleware(EnsureUserIsAdmin::class);
    });


    Route::group([], function () {
        Route::apiResource('posts', PostController::class);
        Route::post('posts/{post}/attach-tags', [PostController::class, 'attachTags']);
        Route::patch('posts/restore/{id}', [PostController::class, 'restore'])
            ->middleware(EnsureUserIsAdmin::class);
        Route::delete('posts/force-delete/{id}', [PostController::class, 'forceDelete']);
        Route::get('posts/{post}/activities', [PostActivityController::class, 'index']);
    });

});