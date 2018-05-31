<div class="">
<?php
    $title = collect($section->section_parameters)->where("section_type_parameter.code","=","textParameter")->first()->value ?? "";
    $size = collect($section->section_parameters)->where("section_type_parameter.code","=","headingNumber")->first()->value ?? "";
    $color = collect($section->section_parameters)->where("section_type_parameter.code","=","color")->first()->value ?? "";

    if (empty($size))
        $size =  '1';

    if (!empty($color))
        $color = "style='color:" . $color . ";'";
?>
{{--@if (!empty($title) && !empty($size))
    <h{{ $size }} {!! $color !!}>
        {{ $title }}
    </h{{ $size }} >
@endif--}}
</div>

@if (!empty($title) && !empty($size))
<div class="row menus-row margin-top-15 margin-bottom-35">
    <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
        <h{{ $size }} {!! $color !!}>
            {{ $title }}
        </h{{ $size }} >
    </div>
</div>
@endif