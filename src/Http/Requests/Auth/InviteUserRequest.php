<?php

namespace Brackets\CraftablePro\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.craftable-pro-user.create');
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string', Rule::unique('craftable_pro_users', 'email')->whereNull('deleted_at')],
            'role_id' => ['required', 'exists:roles,id'],
        ];
    }
}
