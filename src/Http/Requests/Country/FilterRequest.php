<?php

namespace Speca\SpecaCore\Http\Requests\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Speca\SpecaCore\Http\Requests\BaseFilterRequest;

class FilterRequest extends BaseFilterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'country_id' => ['nullable', 'string', 'max:255', 'exists:countries,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'iso_code' => ['nullable', 'string', 'max:255'],
            'phone_code' => ['nullable', 'string', 'max:255'],
            'flag' => ['nullable', 'string', 'max:255'],
            'default_currency' => ['nullable', 'string', 'max:255'],
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
