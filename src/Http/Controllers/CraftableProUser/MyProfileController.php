<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class MyProfileController extends Controller
{
    private $craftableProUser;

    private function setUser(Request $request)
    {
        $this->craftableProUser = $request->user('craftable-pro');
    }

    public function edit(Request $request)
    {
        $this->setUser($request);

        return Inertia::render('CraftableProUser/Profile/Edit', [
            'craftableProUser' => $this->craftableProUser,
            'locales' => getAvailableLocalesTranslated(),
        ]);
    }

    public function update(Request $request)
    {
        $this->setUser($request);

        $validated = $request->validate([
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'locale' => ['sometimes', 'string'],
        ]);

        $this->craftableProUser->update($validated);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Profile successfully updated')]);
    }
}
