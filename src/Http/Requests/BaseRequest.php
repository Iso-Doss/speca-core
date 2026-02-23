<?php

namespace Speca\SpecaCore\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Resources\SendApiResponse;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        /** @var ValidationException $exception */
        $exception = $validator->getException();
        if (isApiRequest($this->getRequestUri())) {
            $response = new SendApiResponse(false, Controller::API_DEFAULT_ERROR_MESSAGE, $validator->errors(), [], $this->all(), [], 422);
            throw (new $exception($validator, $response))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        } else {
            throw (new $exception($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }

    /**
     * Prepare for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([]);
    }
}
