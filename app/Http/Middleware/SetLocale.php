<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request locale, in priority order:
 *   1. ?lang= on the request (the language switcher)
 *   2. the visitor's session
 *   3. the signed-in user's saved preference
 *   4. the browser's Accept-Language header
 *   5. the app default
 *
 * Anything not in config('portfolio.locales') is ignored, so a crafted
 * ?lang=../../ can never reach the translation file loader.
 */
final class SetLocale
{
    public const SESSION_KEY = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        App::setLocale($locale);
        $request->session()->put(self::SESSION_KEY, $locale);

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $supported = array_keys(config('portfolio.locales', []));

        $candidates = [
            $request->query('lang'),
            $request->session()->get(self::SESSION_KEY),
            $request->user()?->locale,
            $request->getPreferredLanguage($supported),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, $supported, true)) {
                return $candidate;
            }
        }

        return config('app.locale');
    }
}
