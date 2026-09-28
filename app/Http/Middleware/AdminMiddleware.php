<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses menu ini.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak! Menu ini khusus untuk Administrator.');
        }

        return $next($request);
    }
}
