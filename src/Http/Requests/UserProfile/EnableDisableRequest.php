<?php

namespace Speca\SpecaCore\Http\Requests\UserProfile;

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
            'user_profile_id' => ['required', 'string', 'max:255', 'exists:user_profiles,id'],
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
            'user_profile_id' => $this->input('user_profile_id', $this->route('userProfileId')),
        ]);
    }
}
