@extends('private._private.index')

@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@section('content')

    @php
    $form = ONE::form('contents', trans('privatePages.details'), 'cm', $type)
            ->settings(["model" => isset($content) ? $content : null])
            ->show('ContentsController@edit', 'ContentsController@delete', ['id' => isset($content) ? $content->content_key : null,'version' => isset($version) ? $version : null], 'ContentsController@index', ['id' => isset($type) ? $type : null,])
            ->create('ContentsController@store', 'ContentsController@index', ['id' => isset($content) ? $content->content_key : isset($type) ? $type : null])
            ->edit('ContentsController@update', 'ContentsController@show', ['id' => isset($content) ? $content->content_key : null, 'version' => isset($version) ? $version : null])
            ->open();
    @endphp

    {!! Form::hidden('content_id', isset($content) ? $content->id : null) !!}
    {!! Form::hidden('type', $type) !!}

    @if(ONE::actionType('contents') == 'show')
        <div style="margin-bottom: 10px;">
            {!! Form::label('versions', trans('privatePages.version')) !!}
            {!! Form::select('versions', isset($versions) ? $versions : null, isset($version) ? $version : $activeVersion, ['class' => 'form-control', 'id' => 'versions', 'onchange' => "getVersion('" . (isset($content) ? $content->content_key : 0) . "')"]) !!}
        </div>
        <div style="margin-bottom: 10px;">
            @if($activeVersion != $version)
                {!! Form::button('<i class="fa fa-check"></i>&nbsp;' . trans('privatePages.activateVersion'), ['class' => 'btn btn-flat btn-success pull-right', 'onclick' => "location.href='".action('ContentsController@activateVersion', [$content->content_key, $version])."'" ]) !!}
            @endif
            {!! Form::button('<i class="fa fa-eye"></i>&nbsp;' . trans('privatePages.preview'), ['class' => 'btn btn-flat btn-info ', 'data-delay' => '{"show":"1000"}', 'data-toggle' => 'tooltip', 'data-original-title' => trans('form.preview'), 'target' => '_blank', 'onclick' => 'window.open("' . URL::action('PublicContentsController@previewPage', [$content->content_key, $version], false) . '")' ]) !!}

            @if(ONE::actionType('contents') == 'show' && $activeVersion == $version)
                @if(!$content->published)
                    {!! Form::button('<i class="fa fa-check"></i>&nbsp;' . trans('privatePages.publish'), ['class' => 'btn btn-flat btn btn-success pull-right ', 'data-delay' => '{"show":"1000"}', 'data-toggle' => 'tooltip', 'data-original-title' => trans('form.publish'), 'onclick' => "location.href='".action('ContentsController@publish', $content->content_key)."'" ]) !!}
                @elseif($content->published)
                    {!! Form::button(trans('privatePages.unPublish'), ['class' => 'btn btn-flat btn btn-danger pull-right ', 'data-delay' => '{"show":"1000"}', 'data-toggle' => 'tooltip', 'data-original-title' => trans('form.unpublish'), 'onclick' => "location.href='".action('ContentsController@unpublish', $content->content_key)."'" ]) !!}
                @endif
                <div style="clear: both"></div>
            @endif
        </div>
    @endif

    {!! Form::oneSelect('content_type_type', trans('privateEvents.page_types'), isset($contentTypesSelect) ? $contentTypesSelect : null, '', isset($content->content_type_type->name) ? $content->content_type_type->name : '', ['class' => 'form-control', 'id' => 'content_type_type']) !!}

    <div class="row">
        <div class="@if(ONE::actionType('contents') == 'show') col-md-8 @endif col-12">
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        <!-- Title -->
                        {!! Form::oneText($language->default == true ? 'required_title_'.$language->code:'title_'.$language->code,
                                          array("name"=>trans('privatePages.title'),"description"=>trans('privatePages.titleDescription')),
                                          isset($translations[$language->code]) ? $translations[$language->code]->title : null,
                                          ['class' => 'form-control', 'id' => 'title_'.$language->code ]) !!}
                        <!-- Summary -->
                        {!! Form::oneTextArea($language->default == true ? 'required_summary_'.$language->code :'summary_'.$language->code,
                                               array("name"=>trans('privatePages.summary'),"description"=>trans('privatePages.summaryDescription')),
                                               isset($translations[$language->code]) ? $translations[$language->code]->summary : null,
                                               ['class' => 'form-control', 'id' => 'summary_'.$language->code, 'rows' => 2]) !!}
                        <!-- Content -->
                        @if(ONE::actionType('contents') == 'show')
                            <!-- Preview with iFrame -->
                            <dt><i class="fa fa-eye"></i> {{ trans('privatePages.preview') }}</dt>
                            <iframe id="previewFrame" src="{{ URL::action('PublicContentsController@previewPage', [$content->content_key, $version, 'langCode' => $language->code], false) }}" style="border:1px solid #999999;width:100%;height:350px;" hspace="0" vspace="0" marginHeight="0" marginWidth="0" frameBorder="0" allowtransparency="true"></iframe>
                            <hr style="margin: 10px 0 10px 0">
                        @else
                            {!! Form::oneTextArea($language->default == true ? 'required_content_'.$language->code : 'content_'.$language->code,
                                                   array("name"=>trans('privatePages.content'),"description"=>trans('privatePages.contentDescription')),
                                                  isset($translations[$language->code]) ? $translations[$language->code]->content : null,
                                                  ['class' => 'form-control content_page', 'id' => 'content_'.$language->code]) !!}
                        @endif
                        <!-- Link -->

                        {{-- Form::hidden('link_'.$language->code, --}}
                        {{--                   trans('privatePages.link'), --!}
                        {{-- isset($translations[$language->code]->link) ? $translations[$language->code]->link : null,  --}}
                        {{--                   ['class' => 'form-control', 'id' => 'link_'.$language->code]) --}}

                        <!-- Buttons are hided (docsMain, docsSide, highlight, slideshow) -->
                        <div style="display:none">
                            <!-- docs main -->
                            <div class="row" >
                                <div class="col-sm-12 col-md-9">
                                    <label for="docs_main_{{$language->code}}">{{trans('privatePages.docsMain')}}</label>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="onoffswitch" readonly>
                                        <input type="checkbox" name="docs_main_{{$language->code}}" class="onoffswitch-checkbox" id="docs_main_{{$language->code}}" value="1"
                                               @if(ONE::actionType('contents') == 'show' ) disabled @endif
                                                {{ (!empty($translations[$language->code]->docs_main) && $translations[$language->code]->docs_main==1) ?  'checked': ''}}>
                                        <label class="onoffswitch-label" for="docs_main_{{$language->code}}" @if(ONE::actionType('contents') == 'show') disabled @endif>
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 10px 0 10px 0">

                            <!-- docs side -->
                            <div class="row" style="display:none;">
                                <div class="col-sm-12 col-md-9">
                                    <label for="docs_side_{{$language->code}}">{{trans('privatePages.docsSide')}}</label>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="onoffswitch" readonly>
                                        <input type="checkbox" name="docs_side_{{$language->code}}" class="onoffswitch-checkbox" id="docs_side_{{$language->code}}" value="1"
                                               @if(ONE::actionType('contents') == 'show' ) disabled @endif
                                                {{ !empty($translations[$language->code]->docs_side) && $translations[$language->code]->docs_side==1 ?  'checked': ''}}>
                                        <label class="onoffswitch-label" for="docs_side_{{$language->code}}" @if(ONE::actionType('contents') == 'show') disabled @endif>
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 10px 0 10px 0">

                            <!-- docs highlight -->
                            <div class="row" style="display:none;">
                                <div class="col-sm-12 col-md-9">
                                    <label for="highlight_{{$language->code}}">{{trans('privatePages.highlight')}}</label>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="onoffswitch" readonly>
                                        <input type="checkbox" name="highlight_{{$language->code}}" class="onoffswitch-checkbox" id="highlight_{{$language->code}}" value="1"
                                               @if(ONE::actionType('contents') == 'show' ) disabled @endif
                                                {{ !empty($translations[$language->code]->highlight) && $translations[$language->code]->highlight==1 ?  'checked': ''}}>
                                        <label class="onoffswitch-label" for="highlight_{{$language->code}}" @if(ONE::actionType('contents') == 'show') disabled @endif>
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 10px 0 10px 0">

                            <!-- slideShow -->
                            <div class="row"  style="display:none;">
                                <div class="col-sm-12 col-md-9">
                                    <label for="slideshow_{{$language->code}}">{{trans('privatePages.slideShow')}}</label>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="onoffswitch" readonly>
                                        <input type="checkbox" name="slideshow_{{$language->code}}" class="onoffswitch-checkbox" id="slideshow_{{$language->code}}" value="1"
                                               @if(ONE::actionType('contents') == 'show' ) disabled @endif
                                                {{ !empty($translations[$language->code]->slideshow) && $translations[$language->code]->slideshow==1 ?  'checked': ''}}>
                                        <label class="onoffswitch-label" for="slideshow_{{$language->code}}" @if(ONE::actionType('contents') == 'show') disabled @endif>
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 10px 0 10px 0">
                        </div>
                    </div>
                    @endforeach
                @php $form->makeTabs(); @endphp
            @endif

        </div>

        <!-- Files, banners and slideshows -->
        @if(ONE::actionType('contents') == 'show')
            <div class="col-md-4 col-12">
                <div class="card" style="border-radius: 0">
                    <div class="card-body with-border">
                        <div class='row'>
                            {{--<div class='col-md-12'>--}}
                                {{--{!! ONE::fileUploadBox("image-drop-zone", trans('files.drop-zone'), trans('files.image'), 'select-image', 'image-list', 'files_image') !!}--}}
                            {{--</div>--}}
                            <div class='col-md-12'>
                                {!! ONE::fileUploadBox("drop-zone", trans('files.drop-zone'), trans('files.files'), 'select-files', 'files-list', 'files') !!}
                            </div>
                            <div class='col-md-12'>
                                {!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}
                            </div>
                            {{--<div class='col-md-12'>--}}
                                {{--{!! ONE::fileUploadBox("slide-drop-zone", trans('files.drop-zone'), trans('files.slideshow'), 'select-slide', 'slide-list', 'files_slide') !!}--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>

                {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('files.resize')) !!}

                {{--@if(isset($file))--}}
                {{--{!! ONE::fileDetailsModal($file, $action, 'fileDetailsModal', 'pages.file_details') !!}--}}
                {{--@endif--}}
        </div>
        @endif
    </div>


    {{--
    @if ($displayLinkableTypes)
        @foreach($linkableTypes as $linkableType)
            {!! Form::oneCheckbox(  $linkableType->id,
                                    isset($linkableType->translations[Session::get('TEMP-LANGUAGE')]->title) ? $linkableType->translations[$presentLanguage]->title : null,
                                    1,
                                    ['class' => 'form-control contentType', 'id' => 'contentType_' .$linkableType->id]) !!}
        @endforeach
    @endif
    --}}

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $( document ).ready(function() {
            {!! ONE::addTinyMCE(".content_page", ['action' => action('ContentsController@getTinyMCE')]) !!}
         });

    </script>
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/canvas-to-blob.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
    <script>

        {!! ONE::fileUploader('fileUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "") !!}
        fileUploader.init();

        {!! ONE::imageUploader('bannerUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 2, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        {{--{!! ONE::imageUploader('slideUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-slide', 'slide-drop-zone', 'slide-list', 'files_slide', 'getCroppedCanvasModal', 0, 3, isset($uploadKey) ? $uploadKey : "") !!}--}}
        {{--slideUploader.init();--}}

        {{--{!! ONE::imageUploader('imageUploader', action('FilesController@upload'), 'contentFileUploaded', 'select-image', 'image-drop-zone', 'image-list', 'files_image', 'getCroppedCanvasModal', 0, 4, isset($uploadKey) ? $uploadKey : "") !!}--}}
        {{--imageUploader.init();--}}

        updateClickListener();

        updateContentList('#files', 1);
        updateContentList('#files_banner', 2);
//        updateContentList('#files_slide', 3);
//        updateContentList('#files_image', 4);

        function getVersion(id) {
            var e = document.getElementById("versions");
            var version = e.options[e.selectedIndex].value;

            window.location = '/private/content/' + id + '/version/'+version;
        }
    </script>
@endsection
