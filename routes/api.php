<?php

use App\Http\Controllers\Api\TodoListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/todo-lists/export', [TodoListController::class, 'exportExcel']);

Route::apiResource('/todo-lists', TodoListController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
