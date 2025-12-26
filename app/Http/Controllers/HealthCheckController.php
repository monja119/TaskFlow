<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Health Check Controller
 * 
 * Provides health check endpoints for monitoring application status
 * Used by Docker health checks and load balancers
 * 
 * @package App\Http\Controllers
 */
class HealthCheckController extends Controller
{
    /**
     * Basic health check endpoint
     * 
     * Returns a simple 200 OK response
     * Used by Docker HEALTHCHECK
     * 
     * @return \Illuminate\Http\Response
     */
    public function basic()
    {
        return response('healthy', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Detailed health check endpoint
     * 
     * Checks all critical services:
     * - Database connection
     * - Cache system
     * - Storage accessibility
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailed(): JsonResponse
    {
        $checks = [
            'app' => $this->checkApplication(),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $isHealthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'checks' => $checks,
        ], $isHealthy ? 200 : 503);
    }

    /**
     * Check application status
     * 
     * @return array
     */
    private function checkApplication(): array
    {
        try {
            return [
                'status' => 'ok',
                'message' => 'Application is running',
                'version' => config('app.version', '1.0.0'),
                'php_version' => PHP_VERSION,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Application check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check database connectivity
     * 
     * @return array
     */
    private function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
                'connection' => config('database.default'),
                'response_time_ms' => $responseTime,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache system
     * 
     * @return array
     */
    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            $value = 'test';

            $startTime = microtime(true);
            Cache::put($key, $value, 10);
            $retrieved = Cache::get($key);
            Cache::forget($key);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrieved !== $value) {
                throw new \Exception('Cache read/write mismatch');
            }

            return [
                'status' => 'ok',
                'message' => 'Cache system operational',
                'driver' => config('cache.default'),
                'response_time_ms' => $responseTime,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache system failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage accessibility
     * 
     * @return array
     */
    private function checkStorage(): array
    {
        try {
            $paths = [
                'logs' => storage_path('logs'),
                'framework' => storage_path('framework'),
                'app' => storage_path('app'),
            ];

            $issues = [];
            foreach ($paths as $name => $path) {
                if (!is_dir($path)) {
                    $issues[] = "$name directory does not exist: $path";
                } elseif (!is_writable($path)) {
                    $issues[] = "$name directory is not writable: $path";
                }
            }

            if (!empty($issues)) {
                return [
                    'status' => 'warning',
                    'message' => 'Storage accessibility issues detected',
                    'issues' => $issues,
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Storage is accessible and writable',
                'paths' => array_keys($paths),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Readiness check endpoint
     * 
     * Checks if the application is ready to receive traffic
     * More comprehensive than basic health check
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function ready(): JsonResponse
    {
        try {
            // Check if migrations are up to date
            $migrationsCheck = $this->checkMigrations();
            
            // Check critical services
            $dbCheck = $this->checkDatabase();
            $cacheCheck = $this->checkCache();

            $isReady = $migrationsCheck['status'] === 'ok' 
                && $dbCheck['status'] === 'ok'
                && $cacheCheck['status'] === 'ok';

            return response()->json([
                'ready' => $isReady,
                'timestamp' => now()->toIso8601String(),
                'checks' => [
                    'migrations' => $migrationsCheck,
                    'database' => $dbCheck,
                    'cache' => $cacheCheck,
                ],
            ], $isReady ? 200 : 503);
        } catch (\Exception $e) {
            return response()->json([
                'ready' => false,
                'error' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Check migration status
     * 
     * @return array
     */
    private function checkMigrations(): array
    {
        try {
            // Simple check - just verify migrations table exists
            $tableExists = DB::getSchemaBuilder()->hasTable('migrations');

            if (!$tableExists) {
                return [
                    'status' => 'error',
                    'message' => 'Migrations table does not exist',
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Migrations are in place',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Migration check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Liveness check endpoint
     * 
     * Simple check to verify the application process is alive
     * Used by Kubernetes liveness probes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function alive(): JsonResponse
    {
        return response()->json([
            'alive' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
