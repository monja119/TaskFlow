<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * List all tokens for the authenticated user
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at?->toDateTimeString(),
                'created_at' => $token->created_at->toDateTimeString(),
                'expires_at' => $token->expires_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'tokens' => $tokens
        ]);
    }

    /**
     * Create a new API token
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $token = $request->user()->createToken(
            $request->name,
            $request->abilities ?? ['*'],
            $request->expires_at ? now()->parse($request->expires_at) : null
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'token_info' => [
                'id' => $token->accessToken->id,
                'name' => $token->accessToken->name,
                'abilities' => $token->accessToken->abilities,
                'expires_at' => $token->accessToken->expires_at?->toDateTimeString(),
            ]
        ], 201);
    }

    /**
     * Revoke a specific token
     */
    public function destroy(Request $request, string $tokenId)
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'message' => 'Token not found'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'Token revoked successfully'
        ]);
    }

    /**
     * Revoke all tokens except the current one
     */
    public function destroyAll(Request $request)
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;
        
        $request->user()->tokens()
            ->where('id', '!=', $currentTokenId)
            ->delete();

        return response()->json([
            'message' => 'All other tokens revoked successfully'
        ]);
    }
}
