<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPackage;

class EnsureUserHasActivePackage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Check if user has an active package
        $hasActivePackage = UserPackage::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->exists();

        if (!$hasActivePackage) {
            session(['redirect_after_purchase' => url()->current()]);
            return redirect()->route('offer')->with('error', 'You need an active subscription or package to access this feature.');
        }

        return $next($request);
    }
}
