@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value ?? ""))
    <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value); ?>
    @if (count($images)>0)
        @foreach($images as $image)
            <a href="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1])}}" class="fancybox-button" rel="fancybox-button">
                <img src="{{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1, "h" => 140, 'quality' => 55])}}" alt="{{ $image->name }}" class="img-thumbnail fancybox image-work" style="max-height:150px;width:auto;">
            </a>
        @endforeach
    @endif
@endif



<script type="text/javascript">
        $(".fancybox-button").fancybox({
            type        : 'image',
            closeBtn    : false,
            openEffect  : 'none',
            closeEffect : 'none',
            prevEffect  : 'none',
            nextEffect  : 'none',
            helpers        : {
                title    : { type : 'inside' },
                buttons    : {}
            }
        });
</script>