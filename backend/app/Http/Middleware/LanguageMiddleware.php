<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

//! Probably a subject to improvement!
class LanguageMiddleware {
    /**
     * Handle an incoming request.
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $locale = $request->header('Accept-Language', config('app.locale', 'en'));
        app()->setlocale($locale);
        return $next($request);
    }
}