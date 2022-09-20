<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// To login
Route::post('login', [AuthController::class, 'login']);
Route::get('payment/{billId}', [PaymentController::class, 'index']);
Route::get('payment-confirmation', [PaymentController::class, 'paymentConfirmation'])->name('paymentConfirmation');

Route::middleware('auth:sanctum', 'verified')->group(function () {

    // To logout
    Route::get('logout', [AuthController::class, 'logout']);

    // List products
    Route::get('product', [ProductController::class, 'index']);

    // Topup
    Route::post('topup', [TransactionController::class, 'topup']);

    // Checkout
    Route::post('checkout', [TransactionController::class, 'checkout']);

});
