<?php

namespace Speca\SpecaCore\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Speca\SpecaCore\Enums\Gender;
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
            'user_id' => ['nullable', 'string', 'max:255', 'exists:users,id'],
            'user_profile_id' => ['nullable', 'string', 'max:255', 'exists:user_profiles,id'],
            'email' => ['nullable', 'email:strict', 'max:255'],
            'phone_with_indicative' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255', Rule::enum(Gender::class)],
            'address' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'string', 'date'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'commercial_name' => ['nullable', 'string', 'max:255'],
            'profession_title' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'string', 'max:255', 'exists:countries,id'],
            'residence_country_id' => ['nullable', 'string', 'max:255', 'exists:countries,id'],
            'nationality_country_id' => ['nullable', 'string', 'max:255', 'exists:countries,id'],
            'role_id' => ['nullable', 'string', 'max:255', 'exists:' . config('permission.table_names.roles') . ',id'],
            'permission_id' => ['nullable', 'string', 'max:255', 'exists:' . config('permission.table_names.permissions') . ',id'],
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
