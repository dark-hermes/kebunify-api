<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerificationEmailController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpertSpecializationController;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login',  [AuthController::class, 'login'])->name('login')->middleware('throttle:6,1');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::post('/email/password', [PasswordResetController::class, 'setResetLinkEmail']);
    Route::post('/email/password-reset', [PasswordResetController::class, 'reset'])->middleware('signed')->name('password.reset');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me',     [AuthController::class, 'me'])->name('me');

    Route::post('email/verification-notification', [VerificationEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('email/verify', [VerificationEmailController::class, 'verify'])->name('verification.verify');

    Route::apiResource('roles', RoleController::class);

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

    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);

    Route::get('chats/{consultation_id}', [ChatController::class, 'index']);
    Route::post('chats/{consultation_id}', [ChatController::class, 'store']);
    Route::get('chats/show/{id}', [ChatController::class, 'show']);
    Route::put('chats/{id}', [ChatController::class, 'update']);
    Route::delete('chats/{id}', [ChatController::class, 'destroy']);
    Route::get('chats/unread-count/{consultation_id}', [ChatController::class, 'unreadCount']);
    Route::put('chats/mark-as-read/{consultation_id}', [ChatController::class, 'markAsRead']);

    Route::get('reviews/{product_id}', [ReviewController::class, 'index']);
    Route::post('reviews/{product_id}', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/random', [ProductController::class, 'random']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/categories/{category_id}/products', [ProductController::class, 'getByCategory']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{id}/related', [ProductController::class, 'getRelated']);
    Route::get('/products/{id}/reviews', [ProductController::class, 'getReviews']);

    Route::apiResource('categories', CategoryController::class);

    Route::get('/sellers', [SellerController::class, 'index']);
    Route::post('/sellers/promote/{user_id}', [SellerController::class, 'promote']);
    Route::get('/sellers/{id}', [SellerController::class, 'show']);
    Route::put('/sellers/{id}', [SellerController::class, 'update']);
    Route::delete('/sellers/{id}', [SellerController::class, 'destroy']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::put('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus']);
    Route::put('/transactions/{transaction}/payment-status', [TransactionController::class, 'updatePaymentStatus']);

    Route::post('/apply-role', [DocumentController::class, 'applyForRole']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::put('/documents/{id}/approve', [DocumentController::class, 'approveApplication']);
    Route::put('/documents/{id}/reject', [DocumentController::class, 'rejectApplication']);
});

Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);