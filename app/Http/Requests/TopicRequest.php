<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TopicRequest extends Request
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
            'marker_pos_x_*' => 'required',
            'parameter_required_*' => 'required',            
            'parameter_maps_required_*' => 'required',
            'start_date' => 'date_format:"Y-m-d"',
            'end_date' => 'date_format:"Y-m-d"|after:start_date'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'parameter_maps_required_*.required' => trans("topics_please_select_on_map_the_location"),            
            'parameter_required_*.required' =>  trans("topics_please_select_one_option"),
            'marker_pos_x_*.required' => trans("topics_please_select_on_map_the_location")
        ];
    }
}
