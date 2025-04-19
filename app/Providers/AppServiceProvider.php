<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
    
            $backendUrl = URL::temporarySignedRoute('verification.verify',now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            /*$backendUrl = http://localhost:8000/verify-email/<id>/<hash>?expires=1745037032&signature=26e7e0884fdf636e65d9103dc3d4*/
    
            $parsedUrl = parse_url($backendUrl);
            /*
            $parsedUrl =[
                'scheme' => 'http',
                'host' => 'localhost',
                'port' => 8000,
                'path' => '/verify-email/3/abc123',
                'query' => 'expires=1745042500&signature=4d1d5b9e7f8c1e2c6cbb229ecf6ce8f3e3733bd9'
            ]
            */
            $query = isset($parsedUrl['query']) ?'?' . $parsedUrl['query'] : '';
    
            return "{$frontendUrl}/verify-email/{$notifiable->getKey()}/" . sha1($notifiable->getEmailForVerification()) . $query;
        });
    }
}
 