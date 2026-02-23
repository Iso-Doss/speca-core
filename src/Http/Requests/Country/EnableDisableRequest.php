<?php

namespace Speca\SpecaCore\Http\Requests\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Speca\SpecaCore\Http\Requests\BaseRequest;

class EnableDisableRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'country_id' => ['required', 'string', 'max:255', 'exists:countries,id'],
            'new_status' => ['required', 'string', 'max:255', 'in:enabled,disabled']
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

        $this->merge([
            'country_id' => $this->input('country_id', $this->route('countryId')),
        ]);
    }
}
