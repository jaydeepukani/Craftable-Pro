<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth\Socialite;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function login($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function callback($driver)
    {
        $userEmail = Socialite::driver($driver)->user()->getEmail();
        $user = config('craftable-pro.craftable_pro_user_model')::query()->whereEmail($userEmail)->first();
        $status = null;
        if ($user) {
            Auth::guard('craftable-pro')->login($user);

            $routeName = app(GeneralSettings::class)->default_route;
            $redirectUrl = url($routeName);

            return Inertia::location($redirectUrl);
        } elseif (config('craftable-pro.social_login.self_registration.enabled', false)) {
            $allowedDomains = config('craftable-pro.social_login.self_registration.allowed_domains', []);
            $domain = collect(explode('@', $userEmail))->last();

            if (in_array('*', $allowedDomains) || in_array($domain, $allowedDomains)) {
                return $this->registerAndSignInUser(Socialite::driver($driver)->user());
            } else {
                $status = __('craftable-pro', 'Given e-mail domain is not enabled.');
            }
        } elseif (! config('craftable-pro.social_login.self_registration.enabled', false)) {
            $status = __('craftable-pro', 'Self registration is not allowed.');
        }

        return Inertia::render('Auth/Login', [
            'status' => $status,
        ]);
    }

    protected function registerAndSignInUser($socialUser)
    {
        $name = collect(explode(' ', $socialUser->getName()));

        $user = config('craftable-pro.craftable_pro_user_model')::create([
            'first_name' => $name->first(),
            'last_name' => $name->count() !== 1 ? $name->last() : null,
            'email' => $socialUser->getEmail(),
            'password' => bcrypt(Str::random(12)),
            'locale' => app(GeneralSettings::class)->default_locale,
            'active' => config('craftable-pro.allow_only_active_users_login', false),
            'invation_sent_at' => now(),
            'invitation_accepted_at' => now(),
        ]);

        $user->markEmailAsVerified();

        $user->assignRole(config('craftable-pro.social_login.self_registration.default_role'));

        Auth::guard('craftable-pro')->login($user);

        $routeName = app(GeneralSettings::class)->default_route;
        $redirectUrl = url($routeName);

        return Inertia::location($redirectUrl);
    }
}
