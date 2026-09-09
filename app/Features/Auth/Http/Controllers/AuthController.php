<?php

namespace App\Features\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', ['loginRole' => request()->query('role')]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'প্রমাণীকরণ ব্যর্থ হয়েছে।',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয়।',
            ])->onlyInput('email');
        }

        $token = auth()->guard('api')->login($user);

        return $this->redirectByRole($user->role)
            ->withCookie(
                cookie('jwt_token', $token, 1440, '/', null, false, true)
            );
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            auth()->guard('api')->invalidate(true);
        } catch (\Exception $e) {
            // JWT token may not exist on web logout
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function apiLogin(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! $token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'প্রমাণীকরণ ব্যর্থ হয়েছে।',
            ], 401);
        }

        $user = auth()->guard('api')->user();

        if (! $user->is_active) {
            auth()->guard('api')->logout();

            return response()->json([
                'message' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয়।',
            ], 403);
        }

        return response()->json([
            'message' => 'সফলভাবে লগইন হয়েছে।',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function apiLogout(Request $request): JsonResponse
    {
        auth()->guard('api')->invalidate(true);

        return response()->json([
            'message' => 'সফলভাবে লগআউট হয়েছে।',
        ]);
    }

    public function apiRefresh(Request $request): JsonResponse
    {
        $token = auth()->guard('api')->refresh();

        return response()->json([
            'message' => 'টোকেন রিফ্রেশ হয়েছে।',
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function apiMe(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin', 'teacher' => redirect()->route('admin.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            default => redirect('/'),
        };
    }
}
