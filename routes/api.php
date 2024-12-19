<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\API\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
// === ITEMS ===

Route::get('/items', [ItemController::class, 'index']);
Route::post('/items/store', [ItemController::class, 'store']);
Route::put('/items/update/{id}', [ItemController::class, 'update']);
Route::delete('/items/delete/{id}', [ItemController::class, 'destroy']);

// === END ITEMS ===

// === TRANSAKSI ===

// Route::get('/transaksi', [TransaksisController::class, 'index']);
Route::get('/transaksi_kasir', [TransaksiController::class, 'getTransactions']);
Route::post('/transaksi/store', [TransaksiController::class, 'store']);
Route::get('/daily_income', [TransaksiController::class, 'daily_income']);
Route::get('/daily_transaction', [TransaksiController::class, 'daily_transaction']);
Route::get('/monthly_income', [TransaksiController::class, 'monthly_income']);
Route::get('/monthly_transaction', [TransaksiController::class, 'monthly_transaction']);

// === END TRANSAKSI ===

// === USERS ===

Route::get('/users', [UserController::class, 'index']);

// === END USERS ===

Route::get('chart', [TransaksiController::class, 'getDataChart']);