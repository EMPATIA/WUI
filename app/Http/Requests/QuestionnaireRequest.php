<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class QuestionnaireRequest extends Request
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
        $rules = [];
        $rules['title'] = 'required';
        $rules['start_date'] = 'required|date';
        if(!empty($_POST['end_date'])){
            $rules['end_date'] = 'required|date|after:start_date';
        }
        return $rules;
    }
}
