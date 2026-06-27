<?php

namespace Frooxi\Shop\Http\Middleware;

use Closure;
use Frooxi\Core\Repositories\LocaleRepository;
use Illuminate\Http\Request;

class Locale
{
    /**
     * Create a middleware instance.
     *
     * @return void
     */
    public function __construct(protected LocaleRepository $localeRepository) {}

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locales = core()->getCurrentChannel()->locales->pluck('code')->toArray();
        $localeCode = core()->getRequestedLocaleCode('locale', false);

        if (! $localeCode || ! in_array($localeCode, $locales)) {
            $localeCode = session()->get('locale');
        }

        if (! $localeCode || ! in_array($localeCode, $locales)) {
            $localeCode = core()->getCurrentChannel()->default_locale->code;
        }

        app()->setLocale($localeCode);
        session()->put('locale', $localeCode);
        unset($request['locale']);

        return $next($request);
    }
}
