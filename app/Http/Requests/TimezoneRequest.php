<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TimezoneRequest extends Request
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
        return [
            'time_code' => 'required | alpha_dash',
            'name' => 'required',
            'code' => 'required | max:4'
        ];
    }
}
