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

    // User tickets
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close']);

    // Replies
    Route::post('tickets/{ticket}/replies', [ReplyController::class, 'store']);

    // Admin
    Route::middleware('admin')->group(function () {
        Route::get('/admin/tickets', [AdminTicketController::class, 'index']);
        Route::patch('/admin/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus']);
    });
});
