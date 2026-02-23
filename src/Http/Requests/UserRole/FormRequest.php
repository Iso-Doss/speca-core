<?php

namespace Speca\SpecaCore\Http\Requests\UserRole;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
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
            'user_role_id' => ['nullable', 'string', 'max:255', 'exists:'.config('permission.table_names.roles').',id'],
            'label' => ['required', 'string', 'max:255', 'unique:'.config('permission.table_names.roles').',label'],
            'name' => ['nullable', 'string', 'max:255', 'unique:'.config('permission.table_names.roles').',name'],
            'description' => ['nullable', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255', 'in:web,api'],
            'user_permissions' => ['nullable', 'array'],
            'user_permissions.*' => ['required', 'string', 'max:255', 'exists:'.config('permission.table_names.permissions').',id'],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'user_role_id' => $this->input('user_role_id', $this->route('userRoleId')),
            'guard_name' => $this->input('guard_name', isApiRequest($this->getRequestUri()) ? 'api' : 'web'),
            'name' => $this->input('name', Str::slug($this->input('label'))),
        ]);
    }
}
