<?php

namespace Speca\SpecaCore\Http\Requests\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Speca\SpecaCore\Http\Requests\BaseRequest;

class FormRequest extends BaseRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
            'code' => ['required', 'string', 'max:255', 'unique:countries,code'],
            'iso_code' => ['required', 'string', 'max:255', 'unique:countries,iso_code'],
            'phone_code' => ['required', 'string', 'max:255', 'unique:countries,phone_code'],
            'flag' => ['nullable', 'string', 'max:255'],
            'default_currency' => ['nullable', 'string', 'max:255', 'in:XOF,XAF'],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'country_id' => $this->input('country_id', $this->route('countryId')),
        ]);
    }
}
