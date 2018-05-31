
<div class="">
    @if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","imagesSingleSection")->first()->value ?? ""))
        <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","imagesSingleSection")->first()->value); ?>
        @if (count($images)>0)
            @foreach($images as $image)
                {{--<a href="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}">--}}
                    <img src="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}" class="img-responsive" style="height:auto;width:auto;" alt="section image">
                {{--</a>--}}
                @break
            @endforeach
        @endif
    @endif
</div>
