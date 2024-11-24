<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\ConsultationTransaction;

Route::get('/', function () {
    return view('welcome');
});


Route::get('payments/{type}/{snap_token}', [PaymentController::class, 'show']);
// Route::get('payments/consultation', [PaymentController::class, 'updateConsultationStatus']);
Route::get('payments/consultation/{snap_token}/status', [PaymentController::class, 'updateConsultationStatus']);
Route::get('payments/product/{snap_token}/status', [PaymentController::class, 'updateProductPaymentStatus']);