
<div class="home-bg-image">
    @if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","imagesSingleSection")->first()->value ?? ""))
        <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","imagesSingleSection")->first()->value); ?>
        @if (count($images)>0)
            @foreach($images as $image)
                {{--<a href="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}">--}}
                    <img src="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1, "w" => 1280])}}" class="img-fluid" style="height:auto;width:auto;" alt="{{ $image->name }}">
                {{--</a>--}}
                @break
            @endforeach
        @endif
    @endif
</div>
