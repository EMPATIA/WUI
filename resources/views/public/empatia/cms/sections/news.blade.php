<div class="container-fluid margin-top-35">
    @foreach($content as $singleContent)
        <?php
            $collectedSections = collect($singleContent->sections);

            $titleText = "";
            $titleSection = $collectedSections->where("section_type.code","=","headingSection")->first();
            if (!empty($titleSection))
                $titleText = collect($titleSection->section_parameters)->where("section_type_parameter.type_code", "=", "text")->first()->value ?? "";


            $image = null;
            $imageSection = $collectedSections->where("section_type.code","=","singleImageSection")->first();
            if (!empty($imageSection)) {
                $imageObject = collect($imageSection->section_parameters)->where("section_type_parameter.type_code", "=", "images_single")->first()->value ?? null;
                if (!empty($imageObject)) {
                    $imageObject = json_decode($imageObject);
                    if (!empty($imageObject))
                        $image = action('FilesController@download',["id"=>$imageObject[0]->id, "code" => $imageObject[0]->code, 1]);
                }
            }

            if (empty($image))
                $image = url('/images/empatia/default_img_contents.jpg');
        ?>
        <a href="{{ action("PublicContentManagerController@show",["contentType" => $contentType, "contentKey" => $singleContent->content_key]) }}">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="news-box-div">
                    <div class="news-inner-img-div news-inner-img-div-left height-200" style="background-image:url({{ $image }})"></div>

                    @if(!empty($singleContent->start_date))
                        <div class="new-date-box">
                            {{ \Carbon\Carbon::parse($singleContent->start_date)->toDateString() }}
                        </div>
                    @endif

                    <div class="news-title-box" style="overflow-wrap: break-word;">
                        {{ $titleText ?? "" }}
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>

<script>
    $(".news-title-box").dotdotdot({
        ellipsis: '... ',
        after: 'a.readmore',
        wrap: 'word',
        aft: null,
        watch: "window",
    });
</script>