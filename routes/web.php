<?php

use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

// Health Check Routes (for monitoring and Docker health checks)
Route::get('/health', [HealthCheckController::class, 'basic'])->name('health.basic');
Route::get('/health/detailed', [HealthCheckController::class, 'detailed'])->name('health.detailed');
Route::get('/health/ready', [HealthCheckController::class, 'ready'])->name('health.ready');
Route::get('/health/alive', [HealthCheckController::class, 'alive'])->name('health.alive');

Route::get('/', function () {
    return view('welcome');
});
