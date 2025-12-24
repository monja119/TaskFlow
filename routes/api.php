<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;

Route::middleware('auth')->group(function () {
	Route::apiResource('projects', ProjectController::class);
	Route::apiResource('tasks', TaskController::class);
});
