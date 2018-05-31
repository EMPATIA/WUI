@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value ?? ""))
    <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value); ?>
    @if (count($images)>0)
        <div id="{{ $section->section_key }}" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach ($images as $image)
                    <li data-target="#{{ $section->section_key }}" data-slide-to="{{ $loop->index }}" @if ($loop->first) class="active" @endif></li>
                @endforeach
            </ol>
            <div class="carousel-inner" role="listbox">
                @forelse($images as $image)
                    <div class="item @if ($loop->first) active @endif">
                        <img src="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}" title="{{ $image->name }}" alt="{{ $image->name }}">
                    </div>
                @empty

                @endforelse
            </div>
            <a class="left carousel-control" href="#{{ $section->section_key }}" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#{{ $section->section_key }}" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    @endif
@endif