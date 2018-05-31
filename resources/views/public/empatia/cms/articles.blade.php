<?php $title =  $content->name; ?>



@if(!empty($content->sections))
    <?php $sections = collect($content->sections); ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">

                <?php $headingContent = collect($content->sections)->where('section_type.code','=','headingSection')->first(); ?>
                @if($headingContent)
                    <div class="col-xs-12">
                        @section('headingSectionSmall')
                            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $headingContent->section_type->code, ['section' => $headingContent])
                        @endsection
                    </div>
                @endif

                <?php $summaryContent = collect($content->sections)->where('section_type.code','=','contentSection')->first(); ?>
                @if($summaryContent)
                    <div class="col-xs-12 pad-0 margin-to-35">
                        <i class="article-summary">
                        @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $summaryContent->section_type->code, ['section' => $summaryContent])
                        </i>
                    </div>

                @endif

                <?php $contentContent = collect($content->sections)->where('section_type.code','=','contentSection')->take(2)->last(); ?>
                @if($contentContent)
                    <div class="row">
                        <div class="col-xs-12">
                            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $contentContent->section_type->code, ['section' => $contentContent])
                        </div>
                    </div>
                @endif


                @foreach($sections as $section)
                    @if($section->section_key != $headingContent->section_key &&
                        $section->section_key != $summaryContent->section_key &&
                        $section->section_key != $contentContent->section_key)


                            <div class="col-xs-12 pad-0">
                                @if($section->section_type->code == 'multipleImagesSection')
                                    @if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value ?? ""))
                                        <?php $images = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleImages")->first()->value); ?>

                                        @if (count($images)>0)
                                            @foreach($images as $image)
                                                <div class="article-image" style="background-image:url({{ action('FilesController@download',["id"=>$image->id, "code" => $image->code, 1,'w' => 1050])}}})">
                                                </div>
                                                @break
                                            @endforeach
                                        @endif
                                    @endif
                                @else
                                    @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $section->section_type->code, ['section' => $section])
                                @endif
                            </div>


                    @endif

                @endforeach


        </div>
        <div class="col-xs-12 col-md-6">

        </div>
    </div>
@endif