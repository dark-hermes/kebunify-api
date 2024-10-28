<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\VerificationEmailController;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login',  [AuthController::class, 'login'])->name('login')->middleware('throttle:6,1');
});

Route::group(['middleware' => 'auth:sanctum'], function () {name:
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me',     [AuthController::class, 'me'])->name('me');

    Route::post('email/verification-notification', [VerificationEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('email/verify', [VerificationEmailController::class, 'verify'])->name('verification.verify');

    Route::apiResource('roles', RoleController::class);

<<<<<<< HEAD
    Route::get('/consultations', [ConsultationController::class, 'index']);
    Route::get('/consultations/user/{user_id}', [ConsultationController::class, 'getByUserId']);
    Route::get('/consultations/{id}', [ConsultationController::class, 'show']);
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::put('/consultations/{id}', [ConsultationController::class, 'update']);
    Route::put('/consultations/change-status/{id}', [ConsultationController::class, 'changeStatus']);
    Route::delete('/consultations/{id}', [ConsultationController::class, 'destroy']);


    Route::get('/experts/leaderboard', [ExpertController::class, 'leaderboard']);
    Route::post('/experts/promote/{user_id}', [ExpertController::class, 'promote']);
    Route::apiResource('experts', ExpertController::class);

    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/upload-avatar', [UserController::class, 'storeAvatar']);
    Route::delete('users/{id}/remove-avatar', [UserController::class, 'removeAvatar']);

    Route::get('consultations', [ConsultationController::class, 'index']);
    Route::get('consultations/user/{user_id}', [ConsultationController::class, 'getByUserId']);
    Route::get('consultations/{id}', [ConsultationController::class, 'show']);
    Route::post('consultations', [ConsultationController::class, 'store']);
    Route::put('consultations/{id}', [ConsultationController::class, 'update']);
    Route::put('consultations/change-status/{id}', [ConsultationController::class, 'changeStatus']);
    Route::delete('consultations/{id}', [ConsultationController::class, 'destroy']);
});
=======


>>>>>>> 272d906dcaa1fabb785e742f7cae72a6de99a567