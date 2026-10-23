<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $name = $request->input('name');

        if (! $name) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun pengguna harus dipilih.',
                ], 422);
            }

            return back()->withErrors(['name' => 'Akun pengguna harus dipilih.']);
        }

        // Cari user berdasarkan 'name' dari dropdown
        $user = User::where('name', $name)->first();

        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tersebut tidak ditemukan di database.',
                ], 422);
            }

            return back()->withErrors(['name' => 'User tersebut tidak ditemukan di database.']);
        }

        // Login session Laravel & simpan ke session 'user'
        Auth::login($user);
        $request->session()->put('user', $user);
        $request->session()->regenerate();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => route('dashboard'),
                'user' => $user,
            ], 200);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout success',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login');
    }
}
