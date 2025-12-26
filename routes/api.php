<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TokenController;

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('api.rate_limit:10,1');
Route::post('/auth/register', [AuthController::class, 'register'])
    ->middleware('api.rate_limit:5,1');

Route::middleware(['auth:sanctum', 'api.rate_limit:120,1'])->group(function () {
	Route::post('/auth/logout', [AuthController::class, 'logout']);
	Route::get('/auth/user', [AuthController::class, 'user']);
	
	Route::apiResource('projects', ProjectController::class);
	Route::apiResource('tasks', TaskController::class);

	// Token management
	Route::get('/tokens', [TokenController::class, 'index']);
	Route::post('/tokens', [TokenController::class, 'store']);
	Route::delete('/tokens/{tokenId}', [TokenController::class, 'destroy']);
	Route::delete('/tokens', [TokenController::class, 'destroyAll']);

});
