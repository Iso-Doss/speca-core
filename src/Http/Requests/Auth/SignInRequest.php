<?php

namespace Speca\SpecaCore\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Speca\SpecaCore\Http\Requests\BaseRequest;

class SignInRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'email' => ['required', 'string', 'max:255', 'email:strict'],
            'password' => ['required', 'string', 'max:255']
        ]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([]);
    }
}
