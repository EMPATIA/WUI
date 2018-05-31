@include('public.default._layouts.cssOverrides')
<?php
$newsContent->sections = collect($newsContent->sections);
$newHeader = $newsContent->sections->where("section_type.code","=","headingSection")->first() ?? "";
if (!empty($newHeader))
    $newHeader = collect($newHeader->section_parameters)->where("section_type_parameter.code","=","textParameter")->first()->value??"";

$newContent = $newsContent->sections->whereIn('section_type.code', ['contentSection','description'])->first();
if (!empty($newContent)) {
    $newContent = strip_tags(collect($newContent->section_parameters)->whereIn("section_type_parameter.code",['newContent','textAreaSection','htmlTextArea'])->first()->value??"");
    if (strlen($newContent)>163)
        $newContent = substr($newContent,0,160) . "...";
}

$newImage = $newsContent->sections->whereIn("section_type.code",["singleImageSection","multipleImagesSection","slideShowSection"])->first();
if (!empty($newImage)) {
    $newImage = collect($newImage->section_parameters)->first()->value;
    if (!empty($newImage)) {
        $newImage = json_decode($newImage)[0]??[];
        if (!empty($newImage))
            $newImage = action('FilesController@download', ['id' => $newImage->id,'code' => $newImage->code, 'inline' => 1]);
    }
}
if (empty($newImage))
    $newImage = "/images/demo/image-1.jpg";
?>
@if(!empty($newsContent))
    <div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">

        <a href="{{ action('PublicContentManagerController@show', ["contentType" => "news", "contentKey" => $newsContent->content_key]) }}" class="a-wrapper">
            <div class="card-img" style="background-image:url('{{ $newImage."?w=350" }}')"></div>
            <div class="title">
                {{ $newHeader }}
            </div>
            <div class="description">
                {!! $newContent !!}
            </div>
            <div class="see-more-btn">
                <hr>
                <div>
                    {{ ONE::transSite("news_see_more") }}
                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
@else
    <div class="row no-gutters margin-top-20">
        <div class="field-wrapper alert alert-info">
            <span class="form-title">{{ ONE::transSite("news_no_news_to_show") }}</span>
        </div>
    </div>
@endif