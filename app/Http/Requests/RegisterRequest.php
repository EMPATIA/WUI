<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\One\One;
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
                'name' => 'name',
                'email' => 'email'

            ];
        }

    }
    public function messages()
    {
        return [
            'required'  => ONE::transSite('the').' :attribute '.ONE::transSite('field_is_required').'.',
            'confirmed' => ONE::transSite('the').' :attribute '.ONE::transSite('confirmation_does_not_match').'.',
            'email'     => ONE::transSite('the').' :attribute '.ONE::transSite('must_be_a_valid_email_address').'.'
        ];
    }
}
