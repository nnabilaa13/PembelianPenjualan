<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard page with analytics.
     */
    public function index(Request $request): View
    {
        $session_raw = $request->session()->get('user');
        $session_user = is_array($session_raw) ? (object) $session_raw : $session_raw;

        $metrics = User::getDashboardMetrics($session_user);

        return view('dashboard', $metrics);
    }
}
