<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        
        // ----------- If the User model implements MustVerifyEmail, 
        // send verification email 
        if ($user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
            //              OR
            // event(new Registered($user));

            return response()->json([
                'success' => 'true',
                'message' => 'Verification link sent to your email address.',
            ], 201);
        }

        // ----------- Else, without MustVerifyEmail implementation
        //  generate loggin token directly
        $token =$user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => 'true',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
