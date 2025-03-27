<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;
use Inertia\Inertia;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('user')->check()) {
            return Inertia::location(route('login'));
        }

        // $lastActivity = session('last_activity_time');
        // $timeout = 5;

        // if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeout) {
        //     Auth::guard('user')->logout();
        //     session()->flush();

        //     return Inertia::location(route('login'));
        // }

        // session(['last_activity_time' => now()]);

        return $next($request);
    }


    // public function handle(Request $request, Closure $next)
    // {
    //     if (!Auth::guard('user')->check()) {
    //         return redirect()->route('login'); 
    //     }

    //     $lastActivity = session('last_activity_time');
    //     $timeout = 5;

    //     if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeout) {
    //         Auth::guard('user')->logout();
    //         session()->flush();
    //         return redirect()->route('login')->withErrors(['message' => 'Session expired.']);
    //     }

    //     session(['last_activity_time' => now()]);

    //     return $next($request);
    // }
}
