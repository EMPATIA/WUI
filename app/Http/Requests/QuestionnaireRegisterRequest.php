<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

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
                'required' => trans('request.the') . ' :attribute ' . trans('request.fieldIsRequired') . '.',
                'email' => trans('request.the') . ' :attribute ' . trans('request.mustBeAvalidEmailAddress') . '.',
                'confirmed' => trans('request.the') . ' :attribute ' . trans('request.confirmationDoesNotMatch') . '.'
            ];
        } else {
            return [
                'required' => trans('request.the') . ' :attribute ' . trans('request.fieldIsRequired') . '.',
                'email' => trans('request.the') . ' :attribute ' . trans('request.mustBeAvalidEmailAddress') . '.'
            ];
        }

    }
}
