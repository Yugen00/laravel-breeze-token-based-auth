<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse
    {   
        /*
            Both requires the MustVerifyEmail impelmentation
            because without it hasVerifiedEmail(), markEmailAsVerified() method doesnot work
        */
        // Case 1: Verifying through dashboard by logged user
        // -- in case when sendEmailVerificationNotification() is not invoked during SIGNUP
        // -- has ability to verify email later
        // -- request with logged 'token' to send verify email
        $authUser = Auth::guard('sanctum')->user();
        if ($authUser) {
            if(! $authUser instanceof MustVerifyEmail){     
                return response()->json([
                    'success'=>'true',
                    'message' => 'Verification is not required!!'
                ]);
            }

            if ($authUser->hasVerifiedEmail()) {
                return response()->json([
                    'success'=>'true',
                    'message'=>'Email Already Verified'
                ]);
            }

            $authUser->sendEmailVerificationNotification();
            /*
            when we invoke sendEmailVerificationNotification() 
            -- laravel internally works through this method 
                URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification())
                ])
            -- it generates
                http://localhost:8000/verify-email/<id>/<hash>?expires=1745037032&signature=26e7e0884fdf636e65d9103dc3d4
                
            But we want to send the 'frontend url' while sending the verify link
            so we intercepet using
                VerifyEmail::createUrlUsing(function ($notifiable) { ... });
            in AppServiceProvider.php in app/Providers
            and attach the frontend url
            */

            return response()->json([
                'success'=>'true',
                'message' => 'verification-link-sent'
            ]);
        }

        // Case 2: User not logged in, email provided
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'success'=>'false',
                'message' => 'Email is not registered! Please signup now.'
            ], 404);
        }

        if(! $user instanceof MustVerifyEmail){
            return response()->json([
                'success'=>'true',
                'message' => 'Verification is not required!!'
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success'=>'true',
                'message'=>'Email Already Verified'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success'=>'true',
            'message' => 'verification-link-sent'
        ]);
    }
}
