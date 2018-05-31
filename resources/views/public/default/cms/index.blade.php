@php
//if(strcasecmp($type,"news")==0)
//    $demoPageTitle = ONE::transSite("news_title");
//elseif(strcasecmp($type,"pages")==0)
//    $demoPageTitle = $content->name ?? ONE::transSite("pages_title");
//
//$imageSection = collect($content->sections)->where("section_type.code","=","singleImageSection")->first();
//if (!empty($imageSection)) {
//    $content->sections = collect($content->sections)->keyBy("id")->forget($imageSection->id)->toArray();
//    $imageObject = collect($imageSection->section_parameters)->where("section_type_parameter.type_code", "=", "images_single")->first()->value ?? null;
//    if (!empty($imageObject)) {
//        $imageObject = json_decode($imageObject);
//        if (!empty($imageObject))
//            $image = action('FilesController@download',["id"=>$imageObject[0]->id, "code" => $imageObject[0]->code, 1]);
//    }
//}else{
//    $image = "/images/demo/workplace-1245776_1920_grey_blured.jpg";
//}
//
//
$demoPageTitle = !empty($content->name) ? $content->name : "";
@endphp
@extends('public.default._layouts.index')

@section('content')
    <?php
    /*$image = null;

    $imageSection = collect($content->sections)->where("section_type.code","=","singleImageSection")->first();
    if (!empty($imageSection)) {
        $content->sections = collect($content->sections)->keyBy("id")->forget($imageSection->id)->toArray();
        $imageObject = collect($imageSection->section_parameters)->where("section_type_parameter.type_code", "=", "images_single")->first()->value ?? null;
        if (!empty($imageObject)) {
            $imageObject = json_decode($imageObject);
            if (!empty($imageObject))
                $image = action('FilesController@download',["id"=>$imageObject[0]->id, "code" => $imageObject[0]->code, 1]);
        }
    }

    if (empty($image))
        $image = url('/images/empatia/default_img_contents.jpg');*/
    ?>
    {{--  <div class="container">
        <div class="row news-title">
            <div class="col title">
                <span>{{ $content->name }}</span>
                <a href="{{ action("PublicContentManagerController@index",$type) }}">back</a>
            </div>
        </div>
    </div>  --}}
    <div class="container news-content">
        <div class="row">
            {{--  <div class="col-lg-4 col-md-5 col-sm-12 col-12 col-image">
                <div class="row image-row">
                    <div class="col-12 news-image" style="background-image: url('{{ $image }}')"></div>
                </div>
            </div>  --}}
            <div class="offset-lg-2 col-lg-8 col-12 col-text" style="margin-top: 55px;margin-bottom: 55px;min-height: 50vh;">
                @include("public.default.cms.embedded")
            </div>
        </div>
    </div>
@endsection