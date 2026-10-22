<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Handle user login via API / AJAX.
     */
    public function login(Request $request): JsonResponse
    {
        $user_name = (string) $request->input('name', '');
        $result = Users::authenticate($user_name);

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Handle user logout via API / AJAX.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $result = Users::processLogout($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result['body'], $result['status']);
        }

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
