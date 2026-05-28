<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminShortcutForLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('get') || ! $request->routeIs('filament.admin.auth.login')) {
            return $next($request);
        }

        if ($request->user() !== null) {
            return $next($request);
        }

        if ($request->session()->pull('admin_login_shortcut_unlocked', false)) {
            return $next($request);
        }

        return redirect()
            ->route('landing')
            ->with('adminShortcutNotice', 'Login admin hanya bisa dibuka lewat shortcut Ctrl + Shift + L.');
    }
}
