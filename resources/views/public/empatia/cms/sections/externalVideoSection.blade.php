@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","url")->first()->value ?? ""))

    <?php $videoTitle = collect($section->section_parameters)->where("section_type_parameter.code","=","video_title")->first(); ?>
    <div class="videoTitle">{{ !empty($videoTitle) ? $videoTitle->value : trans("public.empatia.cms.sections.defaultVideoName") }}</div>
    <div class="video-wrapper">
        <div class="">
            <iframe src="{{ collect($section->section_parameters)->where("section_type_parameter.code","=","url")->first()->value }}" style="width:100%;height:300px;"></iframe>
        </div>
    </div>
@endif

