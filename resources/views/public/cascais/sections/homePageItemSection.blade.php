<?php
    $url = collect($section->section_parameters)->where("section_type_parameter.code","=","url")->first()->value ?? "";
    $cbKey = collect($section->section_parameters)->where("section_type_parameter.code","=","cbKey")->first()->value ?? "";
    $htmlTextArea = collect($section->section_parameters)->where("section_type_parameter.code","=","htmlTextArea")->first()->value ?? "";
    $date = (collect($section->section_parameters)->where("section_type_parameter.code","=","date")->first()->value ?? "");
    $cbType = (collect($section->section_parameters)->where("section_type_parameter.code","=","cbType")->first()->value ?? "");
    $color = collect($section->section_parameters)->where("section_type_parameter.code","=","color")->first()->value ?? "transparent";

    if (empty($url) || Carbon\Carbon::now()>Carbon\Carbon::parse($date))
        $url = action('PublicCbsController@show',["cbKey"=>$cbKey, "type" => $cbType]);
?>

@if(!empty(strip_tags($htmlTextArea)))
    <div style="background-color:{{ $color }};" class="col-12 col-sm-12 col-md-4 op-process-box">
        <a href="{{ $url }}">
            <div class="content normal-text">
                {!! $htmlTextArea !!}
            </div>
        </a>
    </div>
@endif