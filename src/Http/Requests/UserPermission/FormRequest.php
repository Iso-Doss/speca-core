<?php

namespace Speca\SpecaCore\Http\Requests\UserPermission;

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
            'user_permission_id' => ['nullable', 'string', 'max:255', 'exists:' . config('permission.table_names.permissions') . ',id'],
            'label' => ['required', 'string', 'max:255', 'unique:' . config('permission.table_names.permissions') . ',label'],
            'name' => ['nullable', 'string', 'max:255', 'unique:' . config('permission.table_names.permissions') . ',name'],
            'description' => ['nullable', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255', 'in:web,api'],
            'user_permission_categories' => ['nullable', 'array'],
            'user_permission_categories.*' => ['required', 'string', 'max:255', 'exists:' . config('permission.table_names.permission_categories') . ',id'],
            'user_roles' => ['nullable', 'array'],
            'user_roles.*' => ['required', 'string', 'max:255', 'exists:' . config('permission.table_names.roles') . ',id'],
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
            'user_permission_id' => $this->input('user_permission_id', $this->route('userPermissionId')),
            'guard_name' => $this->input('guard_name', isApiRequest($this->getRequestUri()) ? 'api' : 'web'),
            'name' => $this->input('name', Str::slug($this->input('label'))),
        ]);
    }
}
