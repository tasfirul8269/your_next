<?php

namespace Frooxi\Shop\Http\Requests\Customer;

use Frooxi\Customer\Facades\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Define your rules.
     *
     * @var array
     */
    private $rules = [
        'phone' => ['required', 'regex:/^(\+?880|0)?1[3-9][0-9]{8}$/'],
        'password' => 'required|min:6',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return Captcha::getValidations($this->rules);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return Captcha::getValidationMessages();
    }
}
