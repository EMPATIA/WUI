<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EventSponsorRequest extends Request
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
            'name' => 'required',
            'description' => 'required',
            'email' => 'required | email',
            'web_page' => 'required',
            'file_id' => 'required'
        ];
    }
}
