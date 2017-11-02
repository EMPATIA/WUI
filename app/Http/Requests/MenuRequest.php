<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MenuRequest extends Request
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
//        return $rules;
        if(!empty($_POST['link'])){
            $rules['link'] = 'regex:/^(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        }
        foreach($this->request->all() as $key => $input)
        {
            if(strpos($key, "required_") !== false) {
                $rules[$key] = 'required';
            }
        }
        return $rules;
        

        

    }
}
