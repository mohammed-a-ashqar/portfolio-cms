<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the whole admin area: authenticated, not deactivated, and at least
 * a viewer. Deactivating a user takes effect on their very next request
 * rather than whenever their session happens to expire.
 */
final class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('admin.login');
        }

        if (! $user->is_active || ! $user->hasRole(UserRole::Viewer)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => __('admin.auth.inactive')]);
        }

        return $next($request);
    }
}
