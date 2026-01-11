<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\ReplyController;
use App\Http\Controllers\API\AdminTicketController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('get-profiles', [AuthController::class, 'getProfile']);

    // User tickets
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close']);

    // Replies
    Route::post('tickets/{ticket}/replies', [ReplyController::class, 'store']);

    // Admin
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/tickets', [AdminTicketController::class, 'index']);
        Route::post('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus']);
    });
});
