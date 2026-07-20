<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminPermissions;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->hasPermission(AdminPermissions::ACCESS_ADMIN)) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many login attempts. Try again in {$seconds} seconds."]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            AuditLogger::record('admin_login_failed', metadata: ['email' => $credentials['email']], request: $request);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        $request->session()->regenerate();
        RateLimiter::clear($key);

        $user = $request->user();

        if (! $user->is_active || ! $user->hasPermission(AdminPermissions::ACCESS_ADMIN)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            AuditLogger::record('admin_login_denied', $user, metadata: ['email' => $credentials['email']], request: $request);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account cannot access the admin panel.']);
        }

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        AuditLogger::record('admin_login_success', $user, request: $request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        AuditLogger::record('admin_logout', $request->user(), request: $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
