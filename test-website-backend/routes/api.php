<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Auth\AuthController;


Route::apiResource('notes', NoteController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
    Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::patch('/admin/users/{id}/role', [AdminController::class, 'changeUserRole']);
});
