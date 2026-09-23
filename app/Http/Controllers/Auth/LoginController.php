<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // adjust to your view path
    }



    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',    // accepts username or email
            'password' => 'required|string|min:8|max:72',
            'remember' => 'boolean',
        ]);

        $throttleKey = strtolower($credentials['login']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }


    $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $user = \App\Models\User::where($field, $credentials['login'])->first();

    $remember = $request->boolean('remember');

    if (! $user || ! Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $remember)) {
        RateLimiter::hit($throttleKey);

        throw ValidationException::withMessages([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

        RateLimiter::clear($throttleKey);

        // Block non-active accounts
        if ($user->account_status === 'suspended') {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Your account has been suspended: '.($user->suspension_reason ?? 'Contact the administrator.'),
            ]);
        }

         if ($user->status === 'rejected') {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Your account has been rejected. Please contact the administrator for more information.',
            ]);
        }

        if ($user->account_status === 'terminated') {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Your account has been terminated.',
            ]);
        }

        if ($user->status !== 'approved') {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Your account is pending approval.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
        'user' => $user->fresh(),
        'message' => 'Logged in successfully.',
    ]); // adjust redirect target
    }

    public function logout(Request $request)
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Logged out successfully.']);
}

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
