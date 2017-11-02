<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MPOperatorRequest extends FormRequest
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
        if($this->request->has('start_date')){
            $rules = [
                'start_date' => 'required|date',
                'end_date' => 'required|after:start_date'
            ];
        }
        return $rules;
    }


    public function messages()
    {
        return [
            'required'  => trans('request.the').' :attribute '.trans('request.field_is_required').'.',
            'date'     => trans('request.the').' :attribute '.trans('request.must_be_a_date').'.',
            'after'     => trans('request.the').' :attribute '.trans('request.must_be_a_date_after_start').'.'
        ];
    }
}
