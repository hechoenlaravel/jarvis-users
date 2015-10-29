<?php

namespace Modules\Users\Http\Requests;

use Joselfonseca\LaravelApiTools\Http\Requests\ApiRequest as FormRequest;

class ForgotPasswordRequest extends FormRequest
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
        return [
            'email' => 'required|exists:app_users,email'
        ];
    }

}