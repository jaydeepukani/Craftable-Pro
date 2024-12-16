<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Auth\InviteUserRequest;
use Brackets\CraftablePro\Http\Requests\Auth\InviteUserStoreRequest;
use Brackets\CraftablePro\Mail\InvitationUserMail;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CraftableProUserInvitationController extends Controller
{
    public function inviteUser(InviteUserRequest $request)
    {
        $data = $request->validated();

        $user = config('craftable-pro.craftable_pro_user_model')::create([
            'email' => $data['email'],
            'password' => bcrypt(Str::random(12)),
            'locale' => app(GeneralSettings::class)->default_locale,
            'active' => config('craftable-pro.require_email_verified', true) ? false : true,
            'invitation_sent_at' => now(),
            'email_verified_at' => config('craftable-pro.require_email_verified', true) ? null : now(),
        ])->assignRole($data['role_id']);

        static::sendInvitation(
            email: $data['email'],
            userFullName: Auth::user()->first_name . " " . Auth::user()->last_name
        );

        return redirect()->back()->with(['message' => ___("craftable-pro", "User was succesfully invited.")]);
    }

    public function createInviteAcceptationUser($email)
    {
        $user = config('craftable-pro.craftable_pro_user_model')::whereEmail($email)->firstOrFail();

        if (! $user->wasInvited()) {
            return redirect()->route("craftable-pro.login");
        }

        return Inertia::render('Auth/InviteUser', [
            'email' => $email,
        ]);
    }

    public function storeInviteAcceptationUser(InviteUserStoreRequest $request)
    {
        $data = $request->validated();
        $user = config('craftable-pro.craftable_pro_user_model')::whereEmail($data['email'])->first();
        $data['password'] = bcrypt($data['password']);
        $user->update($data + ['active' => true, 'invitation_accepted_at' => now()]);
        $user->markEmailAsVerified();

        return redirect()->route('craftable-pro.login');
    }

    public static function sendInvitation(string $email, string $userFullName)
    {
        Mail::to($email)->send(new InvitationUserMail([
            'email' => $email,
            'userFullName' => $userFullName,
        ]));
    }
}
