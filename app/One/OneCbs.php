<?php

namespace App\One;

use Session;
use Illuminate\Foundation\Bus\DispatchesJobs;



class OneCbs
{
    use DispatchesJobs;

    public function __construct()
    {
    }

    public static function getParameterOption($parameters, $code, $field = null)
    {
        $option = null;
        foreach ( $parameters as $item){
            if($item->code ==  $code){
                $value  = $item->pivot->value;
                foreach ($item->options as $optionItem){
                    if($optionItem->id == $value){
                        $option = $optionItem;
                    }
                }
                if( empty($field) ) {
                    return $option;
                } else {
                    return !empty($option->$field) ? $option->$field : null;
                }
            }
        }
    }

    public static function getCbTopicsCategoriesAndMarkers($topics)
    {
        $information = [];

        foreach ($topics as $topic){
            if($categoryName = OneCbs::getParameterOption($topic->parameters, "category", "label")){
                if($categoryPin = OneCbs::getParameterOption($topic->parameters, "category", "pin")){
                    $information[$categoryName] = json_decode($categoryPin);
                }else{
                    $information[$categoryName] = 'default';
                }
            }
        }

        return $information;
    }
}