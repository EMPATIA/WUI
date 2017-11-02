<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Session;

class RegisterRequest extends Request
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
        if(empty(Session::get("SITE-CONFIGURATION.boolean_no_email_needed")) || $this->get("withoutemail",0)==0){
            return [
                'name' => 'required ',
                'email' => 'required | email',
                'password' => 'required | confirmed',

            ];
        }else{
            return [
                'name' => 'required ',
                'email' => 'email'

            ];
        }

    }
    public function messages()
    {
        return [
            'required'  => trans('request.the').' :attribute '.trans('request.fieldIsRequired').'.',
            'confirmed' => trans('request.the').' :attribute '.trans('request.confirmationDoesNotMatch').'.',
            'email'     => trans('request.the').' :attribute '.trans('request.mustBeAvalidEmailAddress').'.'
        ];
    }
}
