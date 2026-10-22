<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }

        $users = Users::all();

        return view('auth.login', compact('users'));
    }
}
