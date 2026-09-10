<?php

namespace App\Features\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        return view('auth.login', ['loginRole' => $request->query('role')]);
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

        /** @var User|null $user */
        $user = Auth::user();

        if ($user === null || ! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয়।',
            ])->onlyInput('email');
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException) {
            return back()->withErrors([
                'email' => 'লগইন সেশন তৈরি করা যায়নি, আবার চেষ্টা করুন।',
            ])->onlyInput('email');
        }

        return $this->redirectByRole($user->role)
            ->withCookie(
                cookie('jwt_token', $token, 1440, '/', null, false, true)
            );
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            JWTAuth::invalidate(true);
        } catch (JWTException) {
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
        try {
            JWTAuth::invalidate(true);
        } catch (JWTException) {
            return response()->json([
                'message' => 'লগআউট ব্যর্থ হয়েছে বা টোকেন ইতিমধ্যে অবৈধ।',
            ], 401);
        }

        return response()->json([
            'message' => 'সফলভাবে লগআউট হয়েছে।',
        ]);
    }

    public function apiRefresh(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::refresh();
        } catch (JWTException) {
            return response()->json([
                'message' => 'টোকেন রিফ্রেশ করা যায়নি, পুনরায় লগইন করুন।',
            ], 401);
        }

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
            default => redirect()->route('admin.dashboard'),
        };
    }
}
