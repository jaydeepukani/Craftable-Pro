<?php

namespace Brackets\CraftablePro\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackLastActive
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('craftable-pro.track_user_last_active_time') || ! $request->user('craftable-pro')) {
            return $next($request);
        }

        if (! $request->user('craftable-pro')->last_active_at || $request->user('craftable-pro')->last_active_at->isPast()) {
            $request->user('craftable-pro')->last_active_at = now();

            $request->user('craftable-pro')->saveQuietly();
        }

        return $next($request);
    }
}
