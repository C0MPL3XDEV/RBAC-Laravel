<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
   Route::get('/me', [AuthController::class, 'me']);
   Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'permission:view-roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'permission:create-roles'])->group(function () {
    Route::post('/roles/create', [RoleController::class, 'store']);
});
