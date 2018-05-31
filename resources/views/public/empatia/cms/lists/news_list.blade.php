<div class="container-fluid news-container">
    <div class="row" style="">
        @foreach($contents as $content)
            <?php
                $collectedSections = collect($content->sections);

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
            <div class="paddingBlock">
                <div class="equalHMWrap eqWrap">
                    <a href="{{ action('PublicContentManagerController@show', ["contentType" => $contentType, "contentKey" => $content->content_key]) }}" class="col-md-12">
                        <div class="col-md-12 my-news-list">
                            <div class="row">
                                <div class="col-sm-3 news-inner-img-div my-news-inner-img-div-left"
                                    style="background-image:url('{{ $image }}')"></div>

                                <div class="col-sm-9">
                                    <p class="color-black">{{ $titleText ?? "" }}</p>
                                    @if(!empty($content->start_date))
                                        <p class="color-ccc"><small>{{ \Carbon\Carbon::parse($content->start_date)->toDateString() }}</small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach

        @if(!empty($nextPage))
            <div class="col-xs-12">
                <a class='jscroll-next' href='{{ URL::action('PublicContentManagerController@index', ["contentType" => $contentType, "page" => $nextPage]) }} '>
                    {{ trans("pages.next") }}
                </a>
            </div>
        @endif
    </div>
</div>