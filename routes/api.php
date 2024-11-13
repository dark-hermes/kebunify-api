<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ExpertEducationController;
use App\Http\Controllers\ExpertExperienceController;
use App\Http\Controllers\VerificationEmailController;
use App\Http\Controllers\ExpertSpecializationController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\TagsController;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login',  [AuthController::class, 'login'])->name('login')->middleware('throttle:6,1');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::post('/email/password', [PasswordResetController::class, 'setResetLinkEmail']);
    Route::post('/email/password-reset', [PasswordResetController::class, 'reset'])->middleware('signed')->name('password.reset');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me',     [AuthController::class, 'me'])->name('me');
    Route::put('me',     [AuthController::class, 'update'])->name('update');
    Route::put('me/password', [AuthController::class, 'changePassword'])->name('change-password');

    Route::post('email/verification-notification', [VerificationEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('email/verify', [VerificationEmailController::class, 'verify'])->name('verification.verify');

    Route::get('roles/list', [RoleController::class, 'list']);
    Route::apiResource('roles', RoleController::class);

    Route::name('users.')->group(function () {
        Route::get('users/export', [UserController::class, 'export'])->name('export');
        Route::apiResource('users', UserController::class)->names([
            'index' => 'index',
            'store' => 'store',
            'show' => 'show',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
        Route::put('users/{id}/switch-status', [UserController::class, 'switchStatus'])->name('switch-status');

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

    Route::get('experts/{expertId}/educations', [ExpertEducationController::class, 'index']);
    Route::post('experts/{expertId}/educations', [ExpertEducationController::class, 'store']);
    Route::post('experts/educations/auth', [ExpertEducationController::class, 'storeAuth']);
    Route::get('experts/{expertId}/educations/{id}', [ExpertEducationController::class, 'show']);
    Route::put('experts/{expertId}/educations/{id}', [ExpertEducationController::class, 'update']);
    Route::put('experts/educations/auth/{id}', [ExpertEducationController::class, 'updateAuth']);
    Route::delete('experts/{expertId}/educations/{id}', [ExpertEducationController::class, 'destroy']);
    Route::delete('experts/educations/auth/{id}', [ExpertEducationController::class, 'destroyAuth']);

    Route::get('experts/{expertId}/experiences', [ExpertExperienceController::class, 'index']);
    Route::post('experts/{expertId}/experiences', [ExpertExperienceController::class, 'store']);
    Route::post('experts/experiences/auth', [ExpertExperienceController::class, 'storeAuth']);
    Route::get('experts/{expertId}/experiences/{id}', [ExpertExperienceController::class, 'show']);
    Route::put('experts/{expertId}/experiences/{id}', [ExpertExperienceController::class, 'update']);
    Route::put('experts/experiences/auth/{id}', [ExpertExperienceController::class, 'updateAuth']);
    Route::delete('experts/{expertId}/experiences/{id}', [ExpertExperienceController::class, 'destroy']);
    Route::delete('experts/experiences/auth/{id}', [ExpertExperienceController::class, 'destroyAuth']);

    Route::apiResource('expert-specializations', ExpertSpecializationController::class);

    Route::apiResource('tags', TagsController::class);

    Route::post('/forum', [ForumController::class, 'store']);
    Route::put('forum/{id}', [ForumController::class, 'update']);
    // Route::post('/forum/{id}/comment', [CommentController::class, 'store']);

    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
    Route::post('articles/{id}/upload-image', [ArticleController::class, 'uploadImage']);

    Route::get('chats/{consultation_id}', [ChatController::class, 'index']);
    Route::post('chats/{consultation_id}', [ChatController::class, 'store']);
    Route::get('chats/show/{id}', [ChatController::class, 'show']);
    Route::put('chats/{id}', [ChatController::class, 'update']);
    Route::delete('chats/{id}', [ChatController::class, 'destroy']);
    Route::get('chats/unread-count/{consultation_id}', [ChatController::class, 'unreadCount']);
    Route::put('chats/mark-as-read/{consultation_id}', [ChatController::class, 'markAsRead']);

    Route::get('reviews/product/{product_id}', [ReviewController::class, 'index']);
    Route::post('reviews/product/{product_id}', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);

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

    Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
});

Route::get('/forum', [ForumController::class, 'index']);
Route::get('/forum/{id}', [ForumController::class, 'show']);
