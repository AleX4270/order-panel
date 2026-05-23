<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Password::defaults(function() {
            $rule = Password::min(8);

            return $this->app->environment('production')
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });

        RateLimiter::for('public', function(Request $request) {
            return Limit::none();

            // if(auth('sanctum')->check()) {
            //     return Limit::none();
            // }

            // return Limit::perHour(20)->by($request->ip() . $request->userAgent());
        });
    }
}
