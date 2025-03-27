<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/login', [UserController::class, 'login']);
Route::post('/api/user/update', [UserController::class, 'update']);
