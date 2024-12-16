<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the two-factor challenge view
     * @param TwoFactorLoginRequest $request
     * @return Response
     */
    public function create(TwoFactorLoginRequest $request)
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('craftable-pro.login'));
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Attempt to authenticate a new session using the two-factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);

            event(new RecoveryCodeReplaced($user, $code));
        } elseif (! $request->hasValidCode()) {
            if ($request->wantsJson()) {
                return response(['message' => __("Invalid verification code.")], 401);
            } else {
                return Inertia::render('Auth/TwoFactorChallenge')->with('status', __("Invalid verification code."));
            }
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        $routeName = app(GeneralSettings::class)->default_route;
        $redirectUrl = url($routeName);

        return Inertia::location($redirectUrl);
    }
}
