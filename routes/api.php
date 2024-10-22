<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationEmailController;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login',  [AuthController::class, 'login'])->name('login')->middleware('throttle:6,1');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me',     [AuthController::class, 'me'])->name('me');

    Route::post('/email/verification-notification', [VerificationEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/email/verify', [VerificationEmailController::class, 'verify'])->name('verification.verify');

    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
});
