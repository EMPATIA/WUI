<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MPRequest extends Request
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
        $rules = ['flowchart_data' => 'required'];

        foreach($this->request->all() as $key => $input)
        {
            if(strpos($key, "required_") !== false) {
                $rules[$key] = 'required';
            }
        }
        return $rules;

    }
}
