<?php

namespace Speca\SpecaCore\Http\Requests\UserPermissionCategory;

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
            'user_permission_category_id' => ['required', 'string', 'max:255', 'exists:'.config('permission.table_names.permission_categories').',id'],
            'new_status' => ['required', 'string', 'max:255', 'in:enabled,disabled'],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'user_permission_category_id' => $this->input('user_permission_category_id', $this->route('userPermissionCategoryId')),
        ]);
    }
}
