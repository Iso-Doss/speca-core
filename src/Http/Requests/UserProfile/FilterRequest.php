<?php

namespace Speca\SpecaCore\Http\Requests\UserProfile;

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
            'user_profile_id' => ['nullable', 'string', 'max:255', 'exists:user_profiles,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
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
