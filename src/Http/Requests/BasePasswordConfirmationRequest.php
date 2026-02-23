<?php

namespace Speca\SpecaCore\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class BasePasswordConfirmationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
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
