<?php

namespace Speca\SpecaCore\Http\Requests\UserPermission;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Speca\SpecaCore\Enums\GroupActionType;

class GroupActionRequest extends FilterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'action' => ['required', 'string', 'max:255', Rule::Enum(GroupActionType::class)],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'action' => $this->input('action', $this->route('action')),
        ]);
    }
}
