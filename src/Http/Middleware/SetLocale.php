<?php

namespace Brackets\CraftablePro\Http\Middleware;

use Brackets\CraftablePro\Settings\GeneralSettings;
use Closure;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if ($request->user('craftable-pro')) {
            app()->setLocale($request->user('craftable-pro')->locale);
        } else {
            app()->setLocale(app(GeneralSettings::class)->default_locale);
        }

        return $next($request);
    }
}
