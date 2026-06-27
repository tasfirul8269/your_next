<?php

namespace Frooxi\Shop\Http\Requests\Customer;

use Frooxi\Customer\Facades\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
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
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'phone' => ['required', 'string', 'regex:/^(\+?880|0)?1[3-9][0-9]{8}$/', 'unique:customers,phone'],
            'password' => 'confirmed|min:6|required',
        ];

        return Captcha::getValidations($rules);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return Captcha::getValidationMessages();
    }
}
