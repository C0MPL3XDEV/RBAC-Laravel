<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

// --------------- AUTH ROUTES --------------- //
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
   Route::get('/me', [AuthController::class, 'me']);
   Route::post('/logout', [AuthController::class, 'logout']);
});
// -------------- END AUTH ROUTES ----------- //

// -------------- ROLE CONTROLLER ROUTES --------- //
Route::middleware(['auth:sanctum', 'permission:view-roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'permission:create-roles'])->group(function () {
    Route::post('/roles/create', [RoleController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'permission:edit-roles'])->group(function () {
    Route::put('/roles/update/{role}', [RoleController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:delete-roles'])->group(function () {
   Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'permission:view-roles'])->group(function () {
    Route::get('/role/{id}', [RoleController::class, 'getRoleById']);
});

Route::middleware(['auth:sanctum', 'permission:assign-permissions'])->group(function () {
    Route::post('/role/{role}/permissions/assign', [RoleController::class, 'assignPermission']);
});

Route::middleware(['auth:sanctum'. 'permission:revoke-permissions'])->group(function () {
    Route::post('/role/{role}/permissions/revoke', [RoleController::class, 'revokePermission']);
});
// ------------ END ROLE CONTROLLER ROUTES ---------- //

// ------------ PERMISSIONS CONTROLLER ROUTES -------- //
Route::middleware(['auth:sanctum', 'permission:view-permissions'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index']);
});

Route::middleware(['auth:sanctum','permission:create-permissions'])->group(function () {
    Route::post('/permissions/create', [PermissionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'permission:update-permissions'])->group(function () {
    Route::put('/permissions/update/{permissionId}', [PermissionController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:delete-permissions'])->group(function () {
    Route::delete('/permissions/delete/{permissionId}', [PermissionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'permission:view-permissions'])->group(function () {
    Route::get('/permissions/{id}', [PermissionController::class, 'getPermissionById']);
});

Route::middleware(['auth:sanctum', 'permission:assign-permissions'])->group(function () {
    Route::post('/permissions/assign/{roleId}', [PermissionController::class, 'assignToRole']);
});

Route::middleware(['auth:sanctum', 'permission:revoke-permissions'])->group(function () {
    Route::post('/permissions/revoke/{roleId}', [PermissionController::class, 'revokeFromRole']);
});
