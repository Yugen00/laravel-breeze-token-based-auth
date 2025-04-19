<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if(!auth()->attempt($request->only('email','password'))){
            return response()->json([
                'success' => 'false',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth()->user();
        
        if ($user instanceof MustVerifyEmail) {
            if (! $user->hasVerifiedEmail()) {
                return response()->json([
                    'success'=>'false',
                    'message' => 'Your email is not verified. Please verify before login !!'
                ], 403);
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'success' => 'true',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['success' => 'true',], 200);
    }
}
