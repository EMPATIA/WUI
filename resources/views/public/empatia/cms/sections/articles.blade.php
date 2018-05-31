<?php $content = collect($content)->sortBy('publish_date'); ?>
@foreach($content as $item)
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margin-top-bottom-15">

        <?php
        $title = '';
        $description = '';
        $image = null;

        $titleSection =  collect($item->sections)->where("section_type.code","=","headingSection")->first();
        if($titleSection){
            $titleParameters = collect($titleSection->section_parameters)->where("section_type_parameter.code","=","textParameter")->first();
            if($titleParameters){
                $title = $titleParameters->value;
            }
        }

        $summarySection =  collect($item->sections)->where("section_type.code","=","contentSection")->first();
        if($summarySection){
            $summaryParameters = collect($summarySection->section_parameters)->first();
            if($summaryParameters){
                $summary = $summaryParameters->value;
            }
        }

        $imageSection = collect($item->sections)->where('section_type.code','=','multipleImagesSection')->first();
        //dd($imageSection);
        if($imageSection){
            $imageParameters = collect($imageSection->section_parameters)->first();
            if($imageParameters){
                $imageJson = $imageParameters->value;
                $image = collect(json_decode($imageJson))->first();
            }
        }

        ?>

        <div class="article-wrapper">
            <div class="article-title"><a href="{{ action('PublicContentManagerController@show', ["contentType"=> 'articles', 'contentKey'=> $item->content_key]) }}">{{ !empty($title) ? $title : trans("public.default.cms.sections.defaultArticleName") }}</a></div>
            <div class="article-text">{!! !empty($summary) ? $summary : trans("public.empatia.cms.sections.defaultArticleDescription") !!} </div>
            @if($image)
                {{--<div class="article-img article-list-image" style="background-image:url({{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1,'w' => 1050])}})">
                </div>--}}
            @endif
            <div class="article-read-more"><a href="{{ action('PublicContentManagerController@show', ["contentType"=> 'articles', 'contentKey'=> $item->content_key]) }}" class="default-btn">{{ trans("public.default.cms.sections.more")  }}</a></div>
        </div>
    </div>

@endforeach


