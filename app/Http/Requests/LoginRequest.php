<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LoginRequest extends Request
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
            'email' => 'required| email',
            'password' => 'required | min:3',
        ];
    }

    public function messages()
    {
        return [
            'required'  => trans('request.the').' :attribute '.trans('request.fieldIsRequired').'.',
            'email'     => trans('request.the').' :attribute '.trans('request.mustBeAvalidEmailAddress').'.'
        ];
    }
}