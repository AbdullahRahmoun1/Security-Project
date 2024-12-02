<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::middleware('rsa-encryption')->get('/hello', function (Request $request) {
    return "hello";
});

// Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/users/{id}', [UserController::class, 'show']);
// });


Route::middleware(['rsa-encryption', 'auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}/balance', [UserController::class, 'updateBalance']);
    Route::get('/users/{id}/transactions', [UserController::class, 'getTransactions']);
});
