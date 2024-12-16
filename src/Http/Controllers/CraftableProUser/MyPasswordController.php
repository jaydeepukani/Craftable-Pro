<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class MyPasswordController extends Controller
{
    private $craftableProUser;

    private function setUser(Request $request)
    {
        $this->craftableProUser = $request->user('craftable-pro');
    }

    public function edit(Request $request)
    {
        $this->setUser($request);

        return Inertia::render('CraftableProUser/Password/Edit', [
            'craftableProUser' => $this->craftableProUser,
        ]);
    }

    public function update(Request $request)
    {
        $this->setUser($request);

        $request->validate([
            'password' => ['required', 'confirmed', Password::default()],
        ]);

        $this->craftableProUser->update([
            'password' => Hash::make($request->get('password')),
        ]);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Password successfully updated')]);
    }
}
