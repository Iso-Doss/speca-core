<?php

namespace Speca\SpecaCore\Http\Requests\User;

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
            'user_id' => ['nullable', 'string', 'max:255', 'exists:users,id'],
            'email' => ['required', 'email:strict', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'user_roles' => ['required', 'array'],
            'user_roles.*' => ['required', 'string', 'exists:'.config('permission.table_names.roles').',id'],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'user_id' => $this->input('user_id', $this->route('userId')),
        ]);
    }
}
