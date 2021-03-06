<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\One\One;

class PasswordUpdateRequest extends Request
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
            'password' => 'required | confirmed',
            'password_confirmation' => 'required'
        ];
        if(isset($_POST['old_password'])){
            $rules['old_password'] = 'required';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'required'  => ONE::transSite('the').' :attribute '.ONE::transSite('field_is_required').'.',
            'confirmed' => ONE::transSite('the').' :attribute '.ONE::transSite('confirmation_does_not_match').'.'
        ];
    }
}
