<?php
    $title = collect($section->section_parameters)->where("section_type_parameter.code","=","heading1")->first()->value ?? "";
    $bgColor = collect($section->section_parameters)->where("section_type_parameter.code","=","color")->first()->value ?? "";

    $buttonText = collect($section->section_parameters)->where("section_type_parameter.code","=","buttonText")->first()->value ?? "";
    $buttonColor = collect($section->section_parameters)->where("section_type_parameter.code","=","buttonColor")->first()->value ?? "";
    $buttonId = uniqid("button-");

    $cbKey = collect($section->section_parameters)->where("section_type_parameter.code","=","cbKey")->first()->value ?? "";
    $cbType = collect($section->section_parameters)->where("section_type_parameter.code","=","cbType")->first()->value ?? "";

    if(empty($cbKey) || empty($cbType))
        $url = collect($section->section_parameters)->where("section_type_parameter.code","=","url")->first()->value ?? "";
    else
        $url = action('PublicCbsController@show', ["cbKey"=>$cbKey, 'type'=> $cbType]);
?>
<div class="container-fluid bg-primary margin-top-20 pagesSection-title hasButton" style="background-color: {{ $bgColor }}!important;">
    <div class="pad-2">
        <div class="col-md-9 pad-0">
           <h1 class="last-ideas-bar-home">{{ $title }}</h1>
        </div>
        <div class="col-md-3 pad-0">

            <a href="{{ $url }}" class="pull-right button-viewAll" id="{{ $buttonId }}">{{ $buttonText }}</a>
        </div>
    </div>
</div>
<style>
    #{{ $buttonId }} {
        background-color: transparent!important;
        color:{{ $buttonColor }}!important;
        border-color: {{ $buttonColor }}!important;
    }
    #{{ $buttonId }}:hover{
        background: {{ $buttonColor }}!important;
        border-color: {{ $buttonColor }}!important;
        color:#fff!important;
    }
</style>