<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Cek apakah role user sesuai dengan yang dibutuhkan
        if ($user->role !== $role) {
            // Jika role tidak sesuai, redirect ke dashboard yang tepat
            if ($user->role === 'gudang') {
                return redirect()->route('gudang.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($user->role === 'dapur') {
                return redirect()->route('dapur.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            // Jika role tidak dikenali, logout dan redirect ke login
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Role pengguna tidak valid.');
        }

        return $next($request);
    }
}
