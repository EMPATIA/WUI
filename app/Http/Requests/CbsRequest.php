<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CbsRequest extends Request
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
            'title' => 'required',
            'start_date' => 'required | date_format:"Y-m-d"',
            'end_date' => 'nullable|date_format:"Y-m-d"|after:start_date'
        ];
    }
}
