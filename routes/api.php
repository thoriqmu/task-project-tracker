<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Projects
    Route::get('projects',       [ProjectController::class, 'index']);
    Route::post('projects',      [ProjectController::class, 'store']);
    Route::get('projects/{id}',  [ProjectController::class, 'show']);
    Route::put('projects/{id}',  [ProjectController::class, 'update']);

    // Tasks
    Route::get('tasks',          [TaskController::class, 'index']);
    Route::post('tasks',         [TaskController::class, 'store']);
    Route::put('tasks/{id}',     [TaskController::class, 'update']);
    Route::delete('tasks/{id}',  [TaskController::class, 'destroy']);
});
