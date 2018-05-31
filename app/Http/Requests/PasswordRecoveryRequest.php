<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\One\One;

class PasswordRecoveryRequest extends Request
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
            'email' => 'required | email'
        ];
    }

    public function messages()
    {
        return [
            'required'  => ONE::transSite('the').' :attribute '.ONE::transSite('field_is_required').'.',
            'email'     => ONE::transSite('the').' :attribute '.ONE::transSite('must_be_a_valid_email_address').'.'
        ];
    }
}
