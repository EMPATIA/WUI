<?php
    $buttonText = collect($section->section_parameters)->where("section_type_parameter.code","=","buttonText")->first()->value ?? "";
    $buttonColor = collect($section->section_parameters)->where("section_type_parameter.code","=","buttonColor")->first()->value ?? "";
    $buttonId = uniqid("button-");
    $alignment = "text-" . (collect($section->section_parameters)->where("section_type_parameter.code","=","alignment")->first()->value ?? "left");

    $url = collect($section->section_parameters)->where("section_type_parameter.code","=","url")->first()->value ?? "";
?>

@if (!empty($buttonText))
    <div class="{{ $alignment }} button-wrapper">
        <a href="{{ $url }}" class="btn btn-primary btn-lg" id="{{ $buttonId }}" style="border:2px solid #FFF;margin-top:10px; ">{{ $buttonText }}</a>
    </div>
    <style>
/*        #{{ $buttonId }} {
            background-color: {{ $buttonColor }};
        }
        #{{ $buttonId }}:hover{
            background: #FFF!important;
            border-color: {{ $buttonColor }}!important;
            color:{{ $buttonColor }}!important;
        }*/
    </style>
@endif