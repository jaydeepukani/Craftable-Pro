<?php

namespace Brackets\CraftablePro\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Redirect;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        Redirect::setIntendedUrl(url()->current());

        if (! $request->expectsJson()) {
            return route('craftable-pro.login');
        }
    }
}
