<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() && !session()->has('profile')) {
            // Store the current URL so we can redirect back after login/register
            session(['intended_url' => $request->fullUrl()]);

            // Redirect to profile creation (you could also redirect to login first if you want)
            return redirect()->route('profile.create');
        }

        return $next($request);
    }
}
