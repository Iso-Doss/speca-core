<?php

namespace Speca\SpecaCore\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class BaseFilterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'activated' => ['nullable', 'boolean'],
            'archived' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],

            // For group action methode.
            'check' => ['nullable', 'array'],
            'uncheck' => ['nullable', 'array'],
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
            'page' => is_null($this->input('page')) ? 1 : $this->input('page'),
            'limit' => is_null($this->input('limit')) ? 10 : $this->input('limit'),
        ]);
    }
}
