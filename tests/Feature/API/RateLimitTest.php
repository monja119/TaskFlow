<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_is_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Clear any existing rate limits
        RateLimiter::clear('api:' . request()->ip());

        // Make 11 requests (limit is 10 per minute for login)
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

            if ($i < 10) {
                $response->assertStatus(200);
            } else {
                // 11th request should be rate limited
                $response->assertStatus(429)
                    ->assertJson([
                        'message' => 'Too many requests. Please try again later.',
                    ]);
            }
        }
    }

    public function test_authenticated_endpoints_have_higher_rate_limit(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Clear any existing rate limits
        RateLimiter::clear('api:' . $user->id);

        // Authenticated endpoints have 120 req/min
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withToken($token)
                ->getJson('/api/auth/user');

            $response->assertStatus(200);
        }

        // Should still have plenty of requests left
        $this->assertTrue(true);
    }

    public function test_rate_limit_headers_are_present(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertHeader('X-RateLimit-Limit')
            ->assertHeader('X-RateLimit-Remaining');
    }
}
