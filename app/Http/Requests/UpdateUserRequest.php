<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$this->route('user')],
            'password' => ['sometimes', 'string', 'min:8', Rules\Password::defaults()],
            'role' => ['sometimes', 'string', 'in:'.implode(',', UserRole::values())],
        ];
    }
}
