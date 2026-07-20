<?php

namespace App\Http\Middleware;

use App\Support\AdminPermissions;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->hasPermission(AdminPermissions::ACCESS_ADMIN)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Your account cannot access the admin panel.']);
        }

        return $next($request);
    }
}
