<div class="document-wrapper">
    <a href="{{ action('FilesController@download',["id"=>$file->id, "code" => $file->code, 1])}}">
        <span class="document-icon">{!! ONE::getFileIcon($file) !!}</span>
        <div class="document-title">{{ $file->name }}</div>
        <div class="document-subtitle">{{ $file->description }}</div>
        <div class="document-number">{{ round($file->size/1024) }} KB</div>
    </a>
</div>
