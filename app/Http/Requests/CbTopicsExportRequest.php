<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CbTopicsExportRequest extends FormRequest
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
            'pad_type' => 'required',
            'pad_selected' => 'required',
        ];
    }


    /** Change error messages that apply to the request
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required'  => trans('request.the').' :attribute '.trans('request.field_is_required').'.',
        ];
    }
}
