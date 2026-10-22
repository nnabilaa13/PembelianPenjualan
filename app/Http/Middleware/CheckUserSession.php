<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->session()->has('user')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir. Silakan login kembali.',
                ], 401);
            }

            return redirect()->route('login')->withErrors(['name' => 'Silakan login terlebih dahulu.']);
        }

        $sessionUser = $request->session()->get('user');
        $userRole = is_array($sessionUser) ? ($sessionUser['role'] ?? '') : ($sessionUser->role ?? '');

        if (! empty($roles) && ! in_array($userRole, $roles, true)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk halaman tersebut.');
        }

        return $next($request);
    }
}
