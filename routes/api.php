<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de user
Route::post('/login', [UserController::class, 'login']);
Route::get('/user/avatar/{filename}', [UserController::class, 'getImage']);
Route::get('/user/detail/{id}', [UserController::class, 'detail']);
Route::post('/user/update', [UserController::class, 'update']);

//Rutas con el middleware de autenticación
Route::middleware([ApiAuthMiddleware::class])->group(function () {
    // rutas del service
    Route::post('/service/store', [ServiceController::class, 'store']);
    Route::put('/service/update/{id}', [ServiceController::class, 'update']);
    Route::delete('/service/delete/{id}', [ServiceController::class, 'destroy']);

    // rutas de categorias
    Route::resource('api/category', CategoryController::class);
    Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    Route::get('/category', [CategoryController::class, 'index'])->withoutMiddleware([ApiAuthMiddleware::class]);

    // Rutas de las imagenes
    Route::post('/image/store', [ImageController::class, 'store']);
    Route::post('/api/image/update/{id}', [ImageController::class, 'update']);
    Route::delete('/api/image/delete/{id}', [ImageController::class, 'destroy']);

});

Route::get('/category/getImage/{filename}', [CategoryController::class, 'getImage']);

// rutas del service
Route::get('/service/{id}', [ServiceController::class, 'show']);
Route::get('/services/getServicesByCategory/{id}', [ServiceController::class, 'getServicesByCategory']);
Route::get('/services/outstanding', [ServiceController::class, 'outstanding']);

// Ruta de las imagenes
Route::get('/image/{filename}', [ImageController::class, 'getImage']);