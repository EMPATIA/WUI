<?php

namespace App\Http\Requests;


class VoteMethodConfigRequest extends Request
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
            'parameter_type' => 'required',
            'name_en' => 'required'
        ];
    }
}
