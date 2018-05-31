@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","singleFiles")->first()->value ?? ""))
    <?php $files = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","singleFiles")->first()->value); ?>
    @if (count($files)>0)
        <div class="bg">
            <div id="column-title">
                <em class="fa fa-file-o"></em>
                {{ ONE::transSite("cms_section_file") }}
            </div>
            <hr>
            @foreach($files as $file)
                <p>
                    <span>
                        <a href="{{ action('FilesController@download',["id"=>$file->id, "code" => $file->code, 1])}}">
                        {!! ONE::getFileIcon($file) !!} {{ $file->name }}
                        </a>
                    </span>
                    <span class="pull-right">{{ round($file->size/1024) }} KB</span>
                </p>
                @break
            @endforeach
        </div>
    @endif
@endif