<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

Route::prefix('auth')
    ->middleware('rsa-encryption')
    ->controller(AuthController::class)
    ->group(function () {
        //auth routes here
        Route::post('login', 'login');
        Route::post('register', 'register');
    });
Route::middleware(['auth:sanctum', 'rsa-encryption'])->group(function () {
    //other routers here
    Route::get('example', function () {
        return [
            'message' => 'hello',
            'request_body' => request()->all()
        ];
    });
    //with encryption
    // Route::get('/users', [UserController::class, 'index']);
    // Route::get('/users/{id}', [UserController::class, 'show']);
    // Route::put('/users/{id}/balance', [UserController::class, 'updateBalance']);
    // Route::get('/users/{id}/transactions', [UserController::class, 'getTransactions']);
});
    //without encryption
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}/balance', [UserController::class, 'updateBalance']);
    Route::get('/users/{id}/transactions', [UserController::class, 'getTransactions']);
    
    Route::get('server-key', function () {
    return response()->json([
        'message' => 'Success',
        'key' => Storage::get('public.pem')
    ]);
});
