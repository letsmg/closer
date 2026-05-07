<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class TermsAcceptanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Routes that don't require terms acceptance
        $excludedRoutes = [
            'terms',
            'privacy',
            'security',
            'robots.txt',
            'sitemap.xml',
            'logout',
        ];

        $currentRoute = $request->route()->getName();
        
        // Skip middleware for excluded routes
        if (in_array($currentRoute, $excludedRoutes)) {
            return $next($request);
        }

        $requiredVersion = (string) config('terms.version', '2026-05-05');
        $acceptedVersion = (string) (
            $request->cookie('terms_accepted_version')
            ?: $request->header('X-Terms-Accepted-Version')
        );

        // Backward compatibility with older single-flag clients.
        $legacyAccepted = (bool) ($request->cookie('terms_accepted') || $request->header('X-Terms-Accepted'));
        $termsAccepted = $acceptedVersion === $requiredVersion || ($legacyAccepted && $acceptedVersion === '');

        if (!$termsAccepted) {
            // Store intended URL for redirect after acceptance
            $intendedUrl = URL::full();
            
            return Redirect::to('/terms?redirect=' . urlencode($intendedUrl))
                        ->with('message', 'You must accept the Terms of Service to continue.');
        }

        return $next($request);
    }
}
