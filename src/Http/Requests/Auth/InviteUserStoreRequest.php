<?php

namespace Brackets\CraftablePro\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InviteUserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'locale' => ['required', 'string'],
        ];
    }
}
