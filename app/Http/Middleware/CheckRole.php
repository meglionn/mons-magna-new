<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:Owner,Admin')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Debug logging
        Log::info('CheckRole Middleware', [
            'user' => $user ? $user->toArray() : null,
            'required_roles' => $roles,
            'is_authenticated' => Auth::check()
        ]);

        // Jika user tidak login, redirect ke login
        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Silakan login terlebih dahulu']);
        }

        // Jika tidak ada role requirement, izinkan akses
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah user role ada dalam daftar role yang diizinkan
        if (in_array($user->Role, $roles)) {
            return $next($request);
        }

        // User tidak memiliki akses
        Log::warning('Access Denied', [
            'user_id' => $user->UserID,
            'user_role' => $user->Role,
            'required_roles' => $roles,
            'route' => $request->path()
        ]);

        abort(403, 'Akses ditolak. Role Anda: ' . $user->Role . '. Role yang diizinkan: ' . implode(', ', $roles));
    }
}