<?php

namespace Speca\SpecaCore\Http\Requests\UserPermission;

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
            'user_permission_id' => ['nullable', 'string', 'max:255', 'exists:' . config('permission.table_names.permissions') . ',id'],
            'label' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255', 'in:web,api'],
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
