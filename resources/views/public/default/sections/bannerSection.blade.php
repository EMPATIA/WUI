<?php
    $title = collect($section->section_parameters)->where("section_type_parameter.code","=","heading1")->first()->value ?? "";
    $subTitle = collect($section->section_parameters)->where("section_type_parameter.code","=","heading4")->first()->value ?? "";
    $color = collect($section->section_parameters)->where("section_type_parameter.code","=","color")->first()->value ?? "transparent";
    $alignment = "text-" . (collect($section->section_parameters)->where("section_type_parameter.code","=","alignment")->first()->value ?? "left");
?>

@if (!empty($title))
    <div style="background-color:{{ $color }};" class="container-fluid">
        <div class="row pad-2 margin-0 {{ $alignment }}">
            <div class="">
                <h1 class="title white">{{ $title }}</h1>
                @if (!empty($subTitle))
                    <h4 class="white">{{ $subTitle }}</h4>
                @endif
            </div>
        </div>
    </div>
@endif