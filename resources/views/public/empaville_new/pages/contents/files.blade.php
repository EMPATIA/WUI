{{--FILES BOTTOM--}}
<div class="row">
    <div class="col-xs-12 files">
        <div id="column-title"><i class="fa fa-file-o"></i> {{ trans('defaultPagesContent.files') }}</div>
        <hr class="contents-box-line">
        @foreach($files as $file)
            <p><span><a
                            href="{{action('FilesController@download', ['id'=>$file->id,'code'=>$file->code])}}/1"
                            title="{{ $file->name }}">
                {!! ONE::getFileIcon($file) !!} {{ $file->name }}</a></span>
                <span class="pull-right">{!! round($file->size/1024) !!}
                    KB</span>
            </p>
        @endforeach
    </div>
</div>
