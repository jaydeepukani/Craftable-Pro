<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Auth\RegisterUserRequest;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $allowedDomains = config('craftable-pro.self_registration.allowed_domains', []);
        $domain = collect(explode('@', $data['email']))->last();

        if (! in_array('*', $allowedDomains) && ! in_array($domain, $allowedDomains)) {
            return back()->withErrors(['message' => __('Your email address is not among the allowed email domains.')]);
        }

        $data['password'] = Hash::make($data['password']);
        $user = config('craftable-pro.craftable_pro_user_model')::create($data);

        $user->assignRole(config('craftable-pro.self_registration.default_role'));

        config('craftable-pro.require_email_verified') ? event(new Registered($user)) : $user->markEmailAsVerified();

        Auth::guard('craftable-pro')->login($user);

        return redirect(app(GeneralSettings::class)->default_route);
    }
}
