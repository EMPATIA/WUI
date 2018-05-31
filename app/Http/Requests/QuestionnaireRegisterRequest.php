<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\One\One;

class QuestionnaireRegisterRequest extends Request
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
        if( Request::has('password') ){
                return [
                    'name' => 'required ',
                    'email' => 'required | email',
                    'password' => 'required|min:3|confirmed',
                    'password_confirmation' => 'required|min:3',
                    'checkboxAcceptTerms' => 'required'
                ];
        } else {
                return [
                    'name' => 'required ',
                    'email' => 'required | email',
                    'checkboxAcceptTerms' => 'required'
                ];
        }
    }

    public function messages()
    {
        if( Request::has('password') ){
            return [
                'required' => ONE::transSite('the') . ' :attribute ' . ONE::transSite('field_is_required') . '.',
                'email' => ONE::transSite('the') . ' :attribute ' . ONE::transSite('must_be_a_valid_email_address') . '.',
                'confirmed' => ONE::transSite('the') . ' :attribute ' . ONE::transSite('confirmation_does_not_match') . '.'
            ];
        } else {
            return [
                'required' => ONE::transSite('the') . ' :attribute ' . ONE::transSite('field_is_required') . '.',
                'email' => ONE::transSite('the') . ' :attribute ' . ONE::transSite('must_be_a_valid_email_address') . '.'
            ];
        }

    }
}
