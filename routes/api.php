<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerificationEmailController;
use App\Http\Controllers\ExpertSpecializationController;


header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login',  [AuthController::class, 'login'])->name('login')->middleware('throttle:6,1');
});

Route::group(['middleware' => 'auth:sanctum'], function () {name:
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me',     [AuthController::class, 'me'])->name('me');

    Route::post('/email/password', [PasswordResetController::class, 'setResetLinkEmail']);
    Route::post('/email/password-reset', [PasswordResetController::class, 'reset'])->middleware('signed')->name('password.reset');

    Route::post('email/verification-notification', [VerificationEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('email/verify', [VerificationEmailController::class, 'verify'])->name('verification.verify');

    Route::apiResource('roles', RoleController::class);

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

    Route::apiResource('expert-specializations', ExpertSpecializationController::class);

    Route::apiResource('experts', ExpertController::class);

    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);

    Route::get('consultations', [ConsultationController::class, 'index']);
    Route::get('consultations/user/{user_id}', [ConsultationController::class, 'getByUserId']);
    Route::get('consultations/{id}', [ConsultationController::class, 'show']);
    Route::post('consultations', [ConsultationController::class, 'store']);
    Route::put('consultations/{id}', [ConsultationController::class, 'update']);
    Route::put('consultations/change-status/{id}', [ConsultationController::class, 'changeStatus']);
    Route::delete('consultations/{id}', [ConsultationController::class, 'destroy']);
});

Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);

Route::name('users.')->group(function () {
    Route::apiResource('users', UserController::class)->names([
        'index' => 'index',
        'store' => 'store',
        'show' => 'show',
        'update' => 'update',
        'destroy' => 'destroy',
    ]);
    Route::post('users/{id}/upload-avatar', [UserController::class, 'storeAvatar'])->name('upload-avatar');
    Route::delete('users/{id}/remove-avatar', [UserController::class, 'removeAvatar'])->name('remove-avatar');
    Route::get('users/{id}/followers', [UserController::class, 'followers'])->name('followers');
    Route::get('users/{id}/following', [UserController::class, 'following'])->name('following');
    Route::post('users/{id}/follow', [UserController::class, 'follow'])->name('follow');
    Route::post('users/{id}/unfollow', [UserController::class, 'unfollow'])->name('unfollow');
});
