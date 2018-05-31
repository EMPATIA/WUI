@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","video")->first()->value ?? ""))
    <?php $videos = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","video")->first()->value); ?>
    @if (count($videos)==1)
        @foreach($videos as $video)
            <div style="height:100%;background-color: black;">
                <video controls style="position: inherit;left: 0;top: 0;min-width: 100%;min-height: 100%;width: 100px;height: 100px;">
                    <source src="{{ action('FilesController@download',["id"=>$video->id, "code" => $video->code, 1])}}" type="{{ $video->type }}">
                </video>
            </div>
            @break
        @endforeach
    @endif
@endif