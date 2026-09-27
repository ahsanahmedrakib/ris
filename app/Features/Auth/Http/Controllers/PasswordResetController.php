<?php

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\Notifications\ResetPasswordNotification;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * The response is identical whether or not the address exists, so this form
     * cannot be used to discover which addresses have accounts.
     */
    private const NEUTRAL_RESPONSE = 'যদি এই ইমেইল ঠিকানাটি সিস্টেমে থাকে, তাহলে একটি রিসেট লিংক পাঠানো হয়েছে।';

    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'ইমেইল ঠিকানা আবশ্যক।',
            'email.email' => 'সঠিক একটি ইমেইল ঠিকানা দিন।',
        ]);

        $status = Password::sendResetLink(
            ['email' => $validated['email']],
            function (User $user, string $token) {
                $user->notify(new ResetPasswordNotification(
                    route('password.reset', [
                        'token' => $token,
                        'email' => $user->email,
                    ])
                ));
            }
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Log::notice('Password reset requested for an unknown address.', [
                'email' => $validated['email'],
            ]);
        }

        return back()->with('status', self::NEUTRAL_RESPONSE);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [
            'token.required' => 'রিসেট টোকেনটি অনুপস্থিত।',
            'email.required' => 'ইমেইল ঠিকানা আবশ্যক।',
            'password.required' => 'নতুন পাসওয়ার্ড আবশ্যক।',
            'password.confirmed' => 'পাসওয়ার্ড দুটি মিলে যায়নি।',
            'password.min' => 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।',
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    // The `hashed` cast on the model does the hashing, and the
                    // model's updating hook bumps token_version for us, which
                    // retires every token and session issued with the old
                    // password. Calling revokeTokens() as well would bump the
                    // version twice for a single reset.
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => ['পাসওয়ার্ড রিসেট লিংকটি অবৈধ অথবা মেয়াদোত্তীর্ণ। অনুগ্রহ করে আবার চেষ্টা করুন।'],
            ]);
        }

        return redirect()->route('login')->with('status', 'পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে। এখন নতুন পাসওয়ার্ড দিয়ে লগইন করুন।');
    }
}
