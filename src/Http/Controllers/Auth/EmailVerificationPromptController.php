<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return $request->user('craftable-pro')->hasVerifiedEmail()
                    ? redirect()->intended(app(GeneralSettings::class)->default_route)
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
