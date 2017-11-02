<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CurrencyRequest extends Request
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
            'currency' => 'required',
            'code' => 'required',
            'decimal_place' => 'required',
            'decimal_point' => 'required',
            'thousand_point' => 'required'
        ];
        if(!empty($_POST['symbol_left'])){
            return $rules;
        }
        else{
            $rules['symbol_right'] = 'required';
            return $rules;
        }
        
    }
}
