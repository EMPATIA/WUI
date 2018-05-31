@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value ?? ""))
    <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value); ?>
    @if (count($images)>0)
        @foreach($images as $image)
            <a href="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}">
                <img src="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}" alt="{{ $image->title }}" class="img-thumbnail" style="max-height:150px;width:auto;">
            </a>
        @endforeach
    @endif
@endif