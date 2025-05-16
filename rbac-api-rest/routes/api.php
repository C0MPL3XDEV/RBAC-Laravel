<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
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
    Route::delete('/permissions/delete/{permission}', [PermissionController::class, 'destroy']);
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
// ------------ END PERMISSIONS CONTROLLER ROUTES ------------- //

// ------------------ USER CONTROLLER ROUTES ----------------- //
Route::middleware(['auth:sanctum', 'permission:view-users'])->group(function () {
   Route::get('/users', [UserController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'permission:create-users'])->group(function () {
    Route::post('/users/create', [UserController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'permission:edit-users'])->group(function () {
    Route::put('/users/update/{user}', [UserController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:delete-users'])->group(function () {
    Route::delete('/users/delete/{user}', [UserController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'permission:view-users'])->group(function () {
   Route::get('/users/{id}', [UserController::class, 'getUserById']);
});
// --------------- END USER CONTROLLER ROUTES ---------------- //

// -------------- PRODUCT CONTROLLER ROUTES ----------------- //
Route::middleware(['auth:sanctum', 'permission:view-products'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'permission:create-products'])->group(function () {
   Route::post('/products/create', [ProductController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'permission:edit-products'])->group(function () {
    Route::put('/products/update/{product}', [ProductController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:delete-products'])->group(function () {
    Route::delete('/products/delete/{product}', [ProductController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'permission:view-orders'])->group(function () {
   Route::get('/product/{id}', [ProductController::class, 'getProductById']);
});
// -------------- END PRODUCT CONTROLLER ------------------ //
