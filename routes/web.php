<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

/* ----- Kept in web.php as these are need to access without 'api' prefix ----- */
// The EmailVerificationNotificationController@store has optional check to 'auth:sanctum'
/*if Model is MustVerifyEmail than 
        -no auth:sanctum(token)
        -user requires email to insert to resend verification link
  if not MustVerifyEmail then
        -user requires auth:sanctum token to send verification link through secured dashboard
*/
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');    

//we don't need to 'auth:sanctum' middleware as verify-email requires 'id' and 'hash' that has all
Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class,'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

