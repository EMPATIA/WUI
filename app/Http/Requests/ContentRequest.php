<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ContentRequest extends Request
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
            'end_date' => 'after_or_equal:'.$this->start_date
        ];

        foreach($this->request->all() as $key => $input)
        {
            if(strpos($key, "required_") !== false) {
                $rules[$key] = 'required';
            }
        }
        return $rules;
    }
    
}
