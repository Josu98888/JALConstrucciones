<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de user
Route::post('/api/login', [UserController::class, 'login']);
Route::post('/api/user/update', [UserController::class, 'update']);
Route::get('/api/user/avatar/{filename}', [UserController::class, 'getImage']);
Route::get('/api/user/detail/{id}', [UserController::class, 'detail']);

//Rutas de category
Route::resource('/api/category', CategoryController::class)->except(['index', 'show'])->middleware('api.auth');
