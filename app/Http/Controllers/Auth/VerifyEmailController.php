<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (! $user || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['success'=>'false','message' => 'Invalid verification link.'], 403);
        }
        
        //create token to send
        $token = $user->createToken('api-token')->plainTextToken;

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success'=>'true',
                'message'=>'Email was already verified !!',
                'token' => $token
            ]);
        }

        if ($user->markEmailAsVerified()) {
            // Ensure the $user is an instance of MustVerifyEmail
            if ($user instanceof MustVerifyEmail) {
                event(new Verified($user));  // Trigger Verified event
            }
        }

        return response()->json([
            'success'=>'true',
            'message'=>'Email is sucessfully verified ',
            'token' => $token
        ]);
    }
}
