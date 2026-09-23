<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SuperAdmin\UserApprovalController;
use App\Http\Controllers\SuperAdmin\UserStatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;


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
    Route::post('/admin/users/{user}/approved', [UserApprovalController::class, 'approved']);
    Route::post('/admin/users/{user}/reject', [UserApprovalController::class, 'reject']);

    Route::post('/admin/users/{user}/terminate', [UserStatusController::class, 'terminate']);
    Route::get('/admin/archived-users', [UserStatusController::class, 'index']);

    Route::post('/admin/users/{user}/suspend', [UserStatusController::class, 'suspend']);
    Route::post('/admin/users/{user}/reactivate', [UserStatusController::class, 'reactivate']);
    Route::post('/admin/users/{user}/terminate', [UserStatusController::class, 'terminate']);

    Route::get('/admin/users', [UserStatusController::class, 'activeUsers']);
});
