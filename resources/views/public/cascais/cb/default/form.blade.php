@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8&libraries=places" type="text/javascript"></script>

    {{--<script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>--}}
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <!-- plupload -->
    {{--<script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>--}}
    {{--<script src="{{ asset("js/cropper.min.js") }}"></script>--}}
    <script src="{{ asset("js/demo/cropper.min.js") }}"></script>
    <script src="{{ asset("js/demo/plupload.full.min.js") }}"></script>
    @include('private._private.pluploadTranslations') {{-- plupload translations --}}
@endsection
@section('header_styles')

    {{--<link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'/>--}}
    <link href="{{ asset("css/demo/cropper.min.css") }}" rel='stylesheet' type='text/css'/>

    <style>
        .oneFormSubmit{
            display: none;
        }

        .custom-form-row {
            padding: 15px 0px;
            padding-top: 15px;
            padding-right: 0px;
            padding-bottom: 15px;
            padding-left: 0px;
            margin-bottom: 5px;
        }

        .custom-form-row .form-group {
            margin-bottom:0;
        }
        
        .files-box .button:hover .upload_files{
            color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }

        .files-box .button a{
            width: 100%;
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: #fff;
            text-align: center;
            padding: 5px 15px !important;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-radius:0;
        }

        .form-empatia .files-col .files-box .button:hover a, .form-empatia .files-col .files-box .button a:hover,
        .files-box .button > a:hover{
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }


        .cancel-btn input,
        .submit-btn input,
        .cancel-btn button,
        .submit-btn button,
        .cancel-btn a,
        .submit-btn a{
            text-align: center;
            padding:5px 15px;
            line-height: 25px !important;
            display: block;
            width: 100%;
            font-size:0.9rem;
        }

        .cancel-btn input,
        .cancel-btn button,
        .cancel-btn a{
            background-color: #4c4c4c;
            color: #fff;
        }

        .cancel-btn input:hover,
        .cancel-btn button:hover,
        .cancel-btn a:hover{
            background-color: #212121 !important;
            color: {{ ONE::getSiteConfiguration("color_secondary") }} !important;
            cursor: pointer;
            text-decoration: none;
        }

        .submit-btn input,
        .submit-btn button,
        .submit-btn a{
            background-color:{{ ONE::getSiteConfiguration("color_primary") }} !important;
            color: #fff !important;
        }


        .submit-btn input:hover,
        .submit-btn button:hover,
        .submit-btn a:hover {
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }} !important;
            cursor: pointer;

        }

        .submit-btn input:hover,
        .submit-btn button:hover,
        .submit-btn a:hover{
            text-decoration: none;
        }

        input[type="checkbox"]{
            width:auto;
        }

        input[type="checkbox"].custom-control-input{
            left: 0;
            top: 7px;
        }

    </style>
@endsection
@section('content')

    <div class="container">
        <div class="row align-items-end idea-topic-title">
            <div clasS="col title">
                <span>{{ONE::transCb('cb_creation_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                <a href="{{ action('PublicCbsController@show', [$cb->cb_key, 'type'=> $type] ) }}">{{ONE::transCb('cb_back', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
            </div>
        </div>
    </div>

    <div class="container-fluid form-empatia light-grey-bg mt-3" style="padding-bottom:70px;">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    @php
                        // form topic
                        $form = ONE::form('topic')
                            ->settings(["model" => isset($topic) ? $topic : null])
                            ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type], 'PublicTopicController@index', ['cbKey' => $cbKey, 'type' => $type])
                            ->create('PublicTopicController@store', 'PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type])
                            ->edit('PublicTopicController@update', 'PublicTopicController@show', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type])
                            ->open()
                    @endphp

                    {!! Form::hidden('type', isset($type) ? $type : '', ['id' => 'type']) !!}

                    {{--<form>--}}
                    <div class="row">
                        @if($errors->any())
                            <div class="col-lg-8 alert alert-danger fade in" style="margin-left:0; text-align:center;">
                                {{trans($errors->first())}}
                            </div>
                        @endif
                        <div class="col-lg-8 col-md-8">
                            <div class="row white-bg custom-form-row">
                                <div clasS="col-lg-4 form-label">
                                    {!! Form::label('title', ONE::transCb('cb_title', !empty($cb) ? $cb->cb_key : $cbKey) . (ONE::isEdit() ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                </div>
                                <div clasS="col-lg-8">
                                    <div class="form-group">
                                        {!! Form::oneText('title', null, isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required', 'maxlength' => '100']) !!}
                                        <small id="titleHelp" class="form-text text-muted"> {!! ONE::transCb('cb_title_help', !empty($cb) ? $cb->cb_key : $cbKey) !!}</small>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="row white-bg custom-form-row">--}}
                                {{--<div clasS="col-lg-4 form-label">--}}
                                    {{--{!! Form::label('summary', ONE::transCb('proposal_summary', !empty($cb) ? $cb->cb_key : $cbKey) . (ONE::isEdit() ? "*" : ""), array('class' => 'label-required color-secundary')) !!}--}}
                                {{--</div>--}}
                                {{--<div clasS="col-lg-8">--}}
                                    {{--<div class="form-group">--}}
                                        {{--{!! Form::oneTextArea('summary', null, isset($topic) ? $topic->summary : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x2', 'style' => 'resize: vertical','required','maxlength' => '1000', 'help_has_tooltip' => true]) !!}--}}
                                        {{--<small id="summaryHelp" class="form-text text-muted"> {{  ONE::transCb('proposal_summary_help', !empty($cb) ? $cb->cb_key : $cbKey) }}</small>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="row white-bg custom-form-row">
                                <div clasS="col-lg-4 form-label">
                                    {!! Form::label('contents', ONE::transCb('cb_description', !empty($cb) ? $cb->cb_key : $cbKey) . (ONE::isEdit() ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                </div>
                                <div clasS="col-lg-8">
                                    <div class="form-group">
                                        {!! Form::oneTextArea('contents', null, isset($topic) ? strip_tags($topic->contents) : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x5', 'style' => 'resize: vertical','required','maxlength' => '1000', 'help_has_tooltip' => true]) !!}
                                        <small id="descriptionHelp" class="form-text text-muted">{!!  ONE::transCb('cb_description_help', !empty($cb) ? $cb->cb_key : $cbKey) !!}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row white-bg custom-form-row">
                                <div clasS="col-lg-4 form-label">
                                    {!! Form::label('contents', ONE::transCb('cb_google_maps', !empty($cb) ? $cb->cb_key : $cbKey) . (ONE::isEdit() ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                </div>
                                <div clasS="col-lg-8">
                                    <div class="form-group">
                                        @if((count($parameters) > 0))
                                            @foreach($parameters as $parameter)
                                                @if(!$parameter['private'])
                                                    @if($parameter["code"] == 'google_maps')
                                                        <div class="row">
                                                            <div class="col-12 map-container">
                                                                <div class="form-group">
                                                                    {!! Form::oneMaps('parameter_'.(($parameter["mandatory"]==1) ? "required_" : "").$parameter['id'], '',isset($parameter['value'])? $parameter['value'] : null,["required" => $parameter["mandatory"], "defaultLocation" => "(".ONE::getSiteConfiguration("maps_default_latitude").",(".ONE::getSiteConfiguration("maps_default_maps_default_longitude").")", "enableSearch" => true, "removeOption" => true, "description" => $parameter['description']]) !!}
                                                                </div>
                                                                <br>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                        {{--<div class="map-container" style="background-image: url('images/googleMap.jpg')"></div>--}}
                                        {{--                                        <a href="#" class="pickLocation-map"><i class="fa fa-map-marker" aria-hidden="true"></i> {{  ONE::getStatusTranslation($translations, 'pick_location') }}</a>--}}
                                        <small id="locationHelp" class="form-text text-muted">{!! ONE::transCb('cb_google_maps_help', !empty($cb) ? $cb->cb_key : $cbKey)  !!}</small>
                                    </div>
                                </div>
                            </div>
                            @if((count($parameters) > 0))
                                @foreach($parameters as $parameter)
                                    @if(!$parameter['private'] && $parameter["code"] != 'google_maps')
                                        <div class="row white-bg custom-form-row">
                                            @if($parameter["code"] == "dropdown" || $parameter['code'] == 'budget' || $parameter['code'] == 'category')

                                                <div class="col-lg-4 form-label">
                                                    {!! Form::label('parameter_'.$parameter['id'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory'] == 1 ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                                </div>
                                                <div class="col-lg-8">
                                                    {!! Form::oneSelect('parameter_'.$parameter['id'], null, $parameter['options'], isset($topicParameters[$parameter['id']])? $topicParameters[$parameter['id']]->pivot->value : null, isset($topicParameters[$parameter['id']])? $parameter['id']['options'][$topicParameters[$parameter['id']]->pivot->value] : null, ['class' => 'form-control border-select',($parameter['mandatory'] == 1)?'required':'notRequired' => 'false','help_has_tooltip' => true] ) !!}
                                                    <span class="small pull-left gray-color help-text">
                                                        {!!  $parameter['description'] !!}
                                                    </span>
                                                </div>
                                            @elseif($parameter["code"] == "text" || $parameter['code'] == 'numeric')
                                            <!-- text -->
                                                <div class="col-lg-4 form-label">
                                                    {!! Form::label('parameter_'.$parameter['id'], $parameter['name'] . (ONE::isEdit()&& $parameter['mandatory'] == 1  ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                                </div>
                                                <div class="col-lg-8">
                                                    <input @if($parameter['code'] == 'numeric') min="0" maxlength="6" @endif class="form-control" type=@if($parameter['code'] == 'numeric') "number" @else "text" @endif @if($parameter['code'] == 'numeric') oninput="this.value=this.value.slice(0,this.maxLength)" @endif
                                                           value="{{isset($topicParameters[$parameter['id']])? $topicParameters[$parameter['id']]->pivot->value : null}}" name="parameter_{!! $parameter['id'] !!}">
                                                    <small id="parameter_.{!! $parameter['id'] !!}" class="form-text text-muted">{!! strip_tags($parameter['description'] ?? '') !!}</small>
                                                </div>
                                            @elseif($parameter["code"] == "text_area")
                                            <!-- text_area -->
                                                <div class="col-lg-4 form-label">
                                                    {!! Form::label('parameter_'.$parameter['id'], $parameter['name'] . (ONE::isEdit()&& $parameter['mandatory'] == 1  ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                                </div>
                                                <div class="col-lg-8">
                                                    {!! Form::oneTextArea('parameter_'.$parameter['id'], null, isset($topicParameters[$parameter['id']])? $topicParameters[$parameter['id']]->pivot->value : null, ['class' => 'form-control', 'maxlength' => '600','size' => '30x2', 'style' => 'resize: vertical',($parameter['mandatory'] == 1)?'required':'', 'help_has_tooltip' => true]) !!}
                                                    <small id="parameter_.{!! $parameter['id'] !!}" class="form-text text-muted">{!! strip_tags($parameter['description'] ?? '') !!}</small>
                                                </div>
                                            @elseif($parameter['code'] == 'radio_buttons')
                                            <!-- radio_buttons -->
                                                <div class="col-lg-4 form-label">
                                                    {!! Form::label('parameter_'.$parameter['id'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory'] == 1  ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                                </div>
                                                <div class="col-lg-8">
                                                    @php $j = 0; @endphp
                                                    @foreach($parameter['options'] as $key => $option)
                                                        <div class="form-group">
                                                            <label class="control control-radio">
                                                                {!! $option !!}
                                                                <input type="radio" name="parameter_{!! $parameter['id'] !!}"
                                                                       value="{!!$key !!}"
                                                                       {{($parameter['mandatory'] == 1)?'required':''}}
                                                                       {{isset($topicParameters[$parameter['id']])? ($topicParameters[$parameter['id']]->pivot->value == $key ? 'checked' : '') : ''}}
                                                                       @if(ONE::actionType('topic') == 'show') disabled @endif

                                                                       @if($parameter['mandatory'] == 1 && $j == 0) required @endif
                                                                >

                                                                <div class="control_indicator"></div>
                                                            </label>
                                                        </div>
                                                        @php $j++; @endphp
                                                    @endforeach
                                                    <small id="parameter_.{!! $parameter['id'] !!}" class="form-text text-muted">{{ strip_tags($parameter['description'] ?? '') }}</small>

                                                </div>
                                            @elseif($parameter['code'] == 'check_box')
                                            <!-- check_box -->
                                                <div class="col-lg-4 form-label">
                                                    {!! Form::label('parameter_'.$parameter['id'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory'] == 1  ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                                </div>
                                                <div class="col-lg-8">
                                                    @php $selectedOptions = []; @endphp
                                                    @if(isset($parameter['value']))
                                                        @php $values = explode(",",$parameter['value']); @endphp
                                                        @if(count($values) > 0)
                                                            @foreach($values as $value)
                                                                @php $selectedOptions[$value] = true; @endphp
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            @php $j = 0; @endphp
                                                            @foreach($parameter['options'] as $key => $option)
                                                                <div class="custom-control custom-checkbox d-block">
                                                                    {{--<label class="checkboxLabel radio-options-label">--}}
                                                                    <input type="checkbox" id="checkboxOption{{$key}}" name="parameter_{!! $parameter['id'] !!}[]" value="{!!$key !!}"
                                                                            {!! isset($selectedOptions[$key]) ?  'checked' : '' !!} class="custom-control-input"
                                                                    >

                                                                    {{--</label>--}}
                                                                    {{--<input id="checkboxOption{{$key}}" type="checkbox"--}}
                                                                    {{--name="parameter_{!! $parameter['id'] !!}[]"--}}
                                                                    {{--value="{!!$key !!}" {!! isset($selectedOptions[$key]) ?  'checked' : '' !!}>--}}

                                                                    {{--</label>--}}
                                                                    <label for="checkboxOption{{$key}}" class="custom-control-label">{!! $option !!}</label>
                                                                    @php $j++; @endphp
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <small id="parameter_.{!! $parameter['id'] !!}" class="form-text text-muted">{!! strip_tags($parameter['description'] ?? '') !!}</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                @endforeach

                            @endif
                        </div>
                        @if(isset($configurations))

                            <div class="col-lg-3 offset-lg-1 col-md-4 files-col">
                                @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-PICTURES')))
                                    <div class="files-box">
                                        <div id="upload-single-image" class="box image-div" style="text-align: center; height: auto">
                                            <img id="image-preview-files2" style="width:100%" src="{!! !empty($jsonFileList[1]) ? action('FilesController@download',[$jsonFileList[1][0]->id, $jsonFileList[1][0]->code]) : "" !!}" >
                                        </div>
                                        <div class="button">
                                            {!!  Form::oneImageUpload("files1", "Image",  !empty($jsonFileList[1]) ? $jsonFileList[1] : [], $uploadKey,
                                                  ["name" => "files[1]",
                                                   "maxfilesize"=> 1,
                                                   "mimetypes"=>"jpg,gif,png",
                                                   "layout"=>"/resources/views/public/default/_macros/oneImageUpload.php",
                                                   "javascript"=>"/resources/views/public/default/_macros/oneImageUploadScript.php"]) !!}
                                        </div>
                                    </div>

                                    <div class="files-box">
                                        <div id="" class="box upload-images">
                                        </div>
                                        <div class="button">
                                            {!!  Form::oneFileUpload("files2", "Files", !empty($jsonFileList[2]) ? $jsonFileList[2] : [], $uploadKey,
                                                  ["name" => "files[2]",
                                                   "maxfilesize"=> 2,
                                                   "mimetypes"=>"jpg,gif,png",
                                                   "layout"=>"/resources/views/public/default/_macros/oneFileUpload.php",
                                                   "javascript"=>"/resources/views/public/default/_macros/oneFileUploadScript.php", "wrapper" => "upload-images", "translation" => ONE::transCb('cb_upload_images', !empty($cb) ? $cb->cb_key : $cbKey), 'filesType'=> 2]) !!}
                                        </div>
                                    </div>
                                    {{--<div class="files-box">--}}
                                    {{--<div class="box image-div" style="background-image: url('images/image-4.jpg')">--}}
                                    {{--</div>--}}
                                    {{--<div class="button">--}}
                                    {{--<a data-toggle="modal" data-target="#add_image" href="#"><i class="fa fa-upload" aria-hidden="true"></i> Change photo</a>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="files-box">--}}
                                    {{--<div class="box">--}}
                                    {{--<div class="file-line">--}}
                                    {{--<i class="fa fa-picture-o" aria-hidden="true"></i> Image.jpg--}}
                                    {{--</div>--}}
                                    {{--<div class="file-line">--}}
                                    {{--<i class="fa fa-picture-o" aria-hidden="true"></i> Image2.jpg--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="button">--}}
                                    {{--<a data-toggle="modal" data-target="#add_image_gallery" href="#"><i class="fa fa-upload" aria-hidden="true"></i> Upload images</a>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                @endif
                                @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-FILES')))
                                    <div class="files-box">
                                        <div class="box box_files">
                                            {{--<div class="file-line">--}}
                                            {{--<i class="fa fa-file" aria-hidden="true"></i> file1.docx--}}
                                            {{--</div>--}}
                                            {{--<div class="file-line">--}}
                                            {{--<i class="fa fa-file" aria-hidden="true"></i> file2.docx--}}
                                            {{--</div>--}}
                                            {{--<div class="file-line">--}}
                                            {{--<i class="fa fa-file" aria-hidden="true"></i> file3.docx--}}
                                            {{--</div>--}}
                                            {{--<div class="file-line">--}}
                                            {{--<i class="fa fa-file" aria-hidden="true"></i> file4.docx--}}
                                            {{--</div>--}}
                                            {{--<div class="file-line">--}}
                                            {{--<i class="fa fa-file" aria-hidden="true"></i> file5.docx--}}
                                            {{--</div>--}}
                                        </div>
                                        <div class="button">
                                            {!! Form::oneFileUpload("files3", "Files", !empty($jsonFileList[3]) ? $jsonFileList[3] : [], isset($uploadKey) ? $uploadKey : "",
                                                                    ["name" => "files[3]",
                                                                     "acceptedtypes"=> "docs",
                                                                     "max_file_size"=> "2mb",
                                                                     "layout"=>"/resources/views/public/default/_macros/oneFileUpload.php",
                                                                     "javascript"=>"/resources/views/public/default/_macros/oneFileUploadScript.php", "wrapper" => "box_files", "translation" => ONE::transCb('cb_upload_files', !empty($cb) ? $cb->cb_key : $cbKey), 'filesType'=> 3]) !!}
                                        </div>
                                    </div>
                                @endif
                                @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-VIDEO-LINK')))
                                    {{--
                                    <div class="files-box">
                                        <div class="box">
                                            <div class="file-line">
                                                <i class="fa fa-file-video-o" aria-hidden="true"></i> video1.docx
                                            </div>
                                        </div>
                                        <div class="button">
                                            <a data-toggle="modal" data-target="#add_video_gallery" href="#"><i class="fa fa-upload" aria-hidden="true"></i> Upload Video</a>
                                        </div>
                                    </div>
                                    --}}
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="row margin-top-20">
                        <div class="col-lg-8 col-md-8 no-padding">
                            <div class="row no-gutters">
                                <div class="col-sm-6 col-12 cancel-btn order-1">
                                    <a href="@if(ONE::isEdit()){{ action('PublicTopicController@show', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type]) }} @else {{ action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type]) }} @endif">{{ONE::transCb('cb_topic_cancel', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                                </div>
                                <div class="col-sm-6 col-12 submit-btn order-0">
                                    <button id="submit_button" type="submit" style="text-align: center;
    padding: 5px 15px;
    line-height: 20px;
    display: block;
    width: 100%;background: {{ ONE::getSiteConfiguration("color_primary") }}; color: white; border: none"><span>@if(ONE::isEdit()) {!!ONE::transCb('cb_topic_save', !empty($cb) ? $cb->cb_key : $cbKey)!!} @else{!! ONE::transCb('cb_demo_store_save', !empty($cb) ? $cb->cb_key : $cbKey) !!} @endif</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--</form>--}}
                    {!! $form->make() !!}
                </div>
            </div>
        </div>
    </div>



    <!-- Fileupload error -->
    <div id="fileUploadError" class="modal" style="z-index: 1055;">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-file-error">
                <div class="card-header card-header-file-error">'
                    <button type="button" class="close pull-right color-white btn-close-error" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="message-body-error">
                        <div><em class="fa fa-exclamation-triangle" aria-hidden="true"></em><span id="fileUploadErrorMsg"
                                                                                                  class="fileUploadErrorMsg"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    var submit_button = document.getElementById("submit_button");
    submit_button.addEventListener("click", function(e) {
        var required = document.querySelectorAll("input[required][type='text']");
        required.forEach(function(element) {
            if(element.value.trim() == "") {
                element.style.backgroundColor = "#f7606f";
            } else {
                element.style.backgroundColor = "white";
            }
        });
        var required = document.querySelectorAll("select[required]");
        required.forEach(function(element) {
            if(element.value.trim() == "") {
                element.style.cssText = "background-color:#f7606f !important;";
            } else {
                element.style.backgroundColor = "white";
            }
        });
        var required = document.querySelectorAll("textarea[required]");
        required.forEach(function(element) {
            if(element.value.trim() == "") {
                element.style.backgroundColor = "#f7606f";
            } else {
                element.style.backgroundColor = "white";
            }
        });

        var required = document.querySelectorAll("input[required][type='radio']");
        required.forEach(function(element) {
            var obj = document.querySelector('input[name="'+element.name+'"]:checked');
            if( obj == null) {
                $( element ).next().css( "border-color", "#f7606f" );
            } else {
                $( element ).next().css( "border-color", "#2E3459" );
            }
        });

        var required = document.querySelectorAll("input[required][type='checkbox']");
        required.forEach(function(element) {
            var obj = document.querySelector('input[name="'+element.name+'"]:checked');
            if( obj == null) {
                $( element ).next().css( "border-color", "#f7606f" );
            } else {
                $( element ).next().css( "border-color", "#2E3459" );
            }
        });

    });
</script>
@endsection