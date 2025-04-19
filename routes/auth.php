<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public (unauthenticated) routes
Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

/*
----- The EmailVerificationNotificationController@store has optional check to 'auth:sanctum'
    if Model is MustVerifyEmail during SIGNUP
        -no auth:sanctum middleware and no token requires
        -user insert email in request body to resend verification link
    if MustVerifyEmail is not compuslory during SIGNUP then
        - send verification email later
        -user requires auth:sanctum token to send verification link through secured dashboard
*/
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');    

//we don't need to 'auth:sanctum' middleware as verify-email requires 'id' and 'hash' that has all
Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class,'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Authenticated (token-based via Sanctum) routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'=>$user->id,
            'name'=> $user->name,
            'email'=> $user->email,
            'email_verified_at'=> $user->email_verified_at
        ]);
    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});