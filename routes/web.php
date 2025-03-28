<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
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
Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::resource('/api/category', CategoryController::class);

    Route::get('/api/category', [CategoryController::class, 'index'])->withoutMiddleware([ApiAuthMiddleware::class]);
});
