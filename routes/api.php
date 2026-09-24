<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SuperAdmin\UserApprovalController;
use App\Http\Controllers\SuperAdmin\UserStatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeviceSelectionController;
use App\Http\Controllers\DeviceController;


Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
});

Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/users/pending', [UserApprovalController::class, 'pending']);
    Route::post('/admin/users/{user}/approve', [UserApprovalController::class, 'approve']);
    Route::post('/admin/users/{user}/reject', [UserApprovalController::class, 'reject']);

    Route::post('/admin/users/{user}/terminate', [UserStatusController::class, 'terminate']);
    Route::get('/admin/user-management/archived', [UserStatusController::class, 'index']);

    Route::post('/admin/user-management/{user}/suspend', [UserStatusController::class, 'suspend']);
    Route::post('/admin/user-management/{user}/reactivate', [UserStatusController::class, 'reactivate']);
    Route::post('/admin/user-management/{user}/terminate', [UserStatusController::class, 'terminate']);

    Route::get('/admin/user-management', [UserStatusController::class, 'activeUsers']);

    Route::apiResource('devices', DeviceController::class);


    Route::get('/device-selection/{type}', [DeviceSelectionController::class, 'index']);
    Route::post('/device-selection/{type}', [DeviceSelectionController::class, 'store']);
    Route::put('/device-selection/{type}/{id}', [DeviceSelectionController::class, 'update']);
    Route::delete('/device-selection/{type}/{id}', [DeviceSelectionController::class, 'destroy']);

    Route::get('/device-models', [DeviceSelectionController::class, 'models']);
    Route::post('/device-models', [DeviceSelectionController::class, 'storeModel']);
    Route::put('/device-models/{id}', [DeviceSelectionController::class, 'updateModel']);
    Route::delete('/device-models/{id}', [DeviceSelectionController::class, 'destroyModel']);
});
