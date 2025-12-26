<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/tokens', [
                'name' => 'Integration Token',
                'abilities' => ['*'],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'token_info' => ['id', 'name', 'abilities']
            ]);

        $this->assertCount(2, $user->fresh()->tokens);
    }

    public function test_user_can_list_their_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('Token 1');
        $user->createToken('Token 2');

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'tokens' => [
                    '*' => ['id', 'name', 'abilities', 'created_at']
                ]
            ]);

        $this->assertCount(3, $response->json('tokens'));
    }

    public function test_user_can_revoke_specific_token(): void
    {
        $user = User::factory()->create();
        $tokenToRevoke = $user->createToken('Token to Revoke');
        $currentToken = $user->createToken('Current Token')->plainTextToken;

        $response = $this->withToken($currentToken)
            ->deleteJson('/api/tokens/' . $tokenToRevoke->accessToken->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token revoked successfully'
            ]);

        $this->assertCount(1, $user->fresh()->tokens);
    }

    public function test_user_cannot_revoke_nonexistent_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson('/api/tokens/999');

        $response->assertStatus(404);
    }

    public function test_user_can_revoke_all_other_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('Token 1');
        $user->createToken('Token 2');
        $currentToken = $user->createToken('Current Token')->plainTextToken;

        $this->assertCount(3, $user->fresh()->tokens);

        $response = $this->withToken($currentToken)
            ->deleteJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'All other tokens revoked successfully'
            ]);

        $this->assertCount(1, $user->fresh()->tokens);
    }
}
