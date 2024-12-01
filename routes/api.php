<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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


Route::middleware(['rsa-encryption','auth:sanctum'])->get('/hello', function (Request $request) {
    return response()->json([
        'data' => [
            'message' => 'Success',
            'key' => Str::random(300)
        ]
    ]);
});

Route::get('server-key',function (){
    return response()->json([
        'message' => 'Success',
        'key' => Storage::get('public.pem')
    ]);
});
