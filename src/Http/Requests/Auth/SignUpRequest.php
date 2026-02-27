<?php

namespace Speca\SpecaCore\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Speca\SpecaCore\Http\Requests\BaseRequest;

class SignUpRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email:strict', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([]);
    }
}
