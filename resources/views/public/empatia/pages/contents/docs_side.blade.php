{{--DOCS--}}
@if (isset($pageContent->docs_side) && $pageContent->docs_side && !empty($files))
    <div style="background-color: #f4f4f4; padding: 10px;">
        <div id="column-title"><i class="fa fa-file-o"></i> {{ trans('PublicContent.files') }}
        </div>
        <hr style="margin: 10px 0px; color: #cccccc">
        @foreach($files as $file)
            <p><span><a
                            href="{{action('FilesController@download', ['id'=>$file->id,'code'=>$file->code])}}"
                            title="{{ $file->name }}">
                        {!! ONE::getFileIcon($file) !!} {{ $file->name }}</a></span>
                        <span class="pull-right">{!! round($file->size/1024) !!}
                            KB</span>
            </p>
        @endforeach
    </div>
@endif