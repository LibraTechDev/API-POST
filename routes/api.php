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

Route::post('/register', [AuthController::class, 'register']); //pass
Route::post('/login', [AuthController::class, 'login']); //pass
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); //pass

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware([EnsureUserIsAdmin::class])->prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']); //pass
        Route::post('/', [UserController::class, 'store']); //pass
        Route::get('/{id}', [UserController::class, 'show']); //pass
        Route::put('/{id}', [UserController::class, 'update']); //pass
        Route::delete('/{id}', [UserController::class, 'destroy']); //pass
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']); //pass
        Route::post('/', [CategoryController::class, 'store']); //pass
        Route::get('/{id}', [CategoryController::class, 'show']); //pass
        Route::put('/{id}', [CategoryController::class, 'update']); //pass
        Route::delete('/{id}', [CategoryController::class, 'destroy']); //pass
        Route::patch('/restore/{id}', [CategoryController::class, 'restore']); //pass
        Route::delete('/force-delete/{id}', [CategoryController::class, 'forceDelete']); //pass
    });

    Route::prefix('tag')->group(function () {
        Route::get('/', [TagController::class, 'index']); //pass
        Route::post('/', [TagController::class, 'store']); //pass
        Route::get('/{id}', [TagController::class, 'show']); //pass
        Route::put('/{id}', [TagController::class, 'update']); //pass
        Route::delete('/{id}', [TagController::class, 'destroy']); //pass
        Route::patch('/restore/{id}', [TagController::class, 'restore'])->middleware(EnsureUserIsAdmin::class); //pass
        Route::delete('/force-delete/{id}', [TagController::class, 'forceDelete'])->middleware(EnsureUserIsAdmin::class); //pass 
    });

    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'index']); //pass
        Route::post('/', [PostController::class, 'store']); //pass
        Route::post('/{post}/attach-tags', [PostController::class, 'attachTags']); //pass
        Route::get('/{id}', [PostController::class, 'show']); //pass
        Route::put('/{id}', [PostController::class, 'update']); //pass
        Route::delete('/{id}', [PostController::class, 'destroy']); //pass
        Route::patch('/restore/{id}', [PostController::class, 'restore'])->middleware(EnsureUserIsAdmin::class); //pass
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']); //pass 
        Route::get('/{post}/activities', [PostActivityController::class, 'index']); //pass
    });

});