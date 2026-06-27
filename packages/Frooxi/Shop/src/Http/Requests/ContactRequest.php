<?php

namespace Frooxi\Shop\Http\Requests;

use Frooxi\Core\Rules\PhoneNumber;
use Frooxi\Customer\Facades\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
        return Captcha::getValidations([
            'name' => 'string|required',
            'email' => 'string|required',
            'contact' => new PhoneNumber,
            'message' => 'required',
        ]);
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
