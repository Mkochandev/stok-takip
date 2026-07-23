<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isExpired()) {
                // İstisna rotaları (Çıkış yapma, profil görme, süresi doldu ekranı)
                $exceptRoutes = [
                    'subscription.expired',
                    'logout',
                    'profile.edit',
                    'profile.update',
                    'profile.destroy',
                ];

                if (!in_array($request->route()?->getName(), $exceptRoutes)) {
                    return redirect()->route('subscription.expired');
                }
            }
        }

        return $next($request);
    }
}
