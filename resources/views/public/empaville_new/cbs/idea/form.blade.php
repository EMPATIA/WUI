@extends('public.empaville_new._layouts.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8&libraries=places" type="text/javascript"></script>
@endsection

@section('content')
    <div class="container createIdea-container">
        <div class="row">
            <div class="col-xs-12">
                <div class="title">
                    <h2 class="bolder">{{strtoupper($cb->title)}}</h2>
                </div>
                <div class="sub-title">
                    <h2 class="bolder">{{trans("empavillePadsIdea.create_idea")}}</h2>
                </div>
            </div>
        </div>

        <div class="row form my-form">


        <?php
        // form topic
        $form = ONE::form('topic')
            ->settings(["model" => isset($topic) ? $topic : null])
            ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type], 'PublicTopicController@index', ['cbKey' => $cbKey, 'type' => $type])
            ->create('PublicTopicController@store', 'PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type])
            ->edit('PublicTopicController@update', 'PublicTopicController@show', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type])
            ->open()
        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <!-- Form details -->
                    <div class="row">

                        <div class="col-xs-12 parameter">
                            {!! Form::oneText('title', trans('empavillePadsIdea.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                        </div>

                        <div class="col-xs-12 parameter">
                            {!! Form::oneTextArea('contents', trans('empavillePadsIdea.description'), isset($topic) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x6', 'style' => 'resize: vertical', 'required' => 'required']) !!}
                        </div>

                        <!-- Hidden fields -->
                        {!! Form::hidden('summary', '', ['id' => 'summary']) !!}
                        {!! Form::hidden('cb_key', isset($topic) ? $topic->id : $cbKey, ['id' => 'cb_key']) !!}
                        {!! Form::hidden('type', isset($type) ? $type : '', ['id' => 'type']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        @foreach($parameters as $param)
                            @if($param["code"] == 'google_maps')
                                {!! Form::oneMaps('parameter_'.(($param["mandatory"]==1) ? "required_" : "").$param['id'],trans('empavillePadsIdea.chose_location'),isset($param['value'])? $param['value'] : null,["required" => $param["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true]) !!}
                            @endif
                            @if($param['code'] == 'image_map')
                                <div class="col-xs-12" id="wrapper">
                                    {!! Form::oneEmpavilleMap( $param['id'], $param['name'], asset('images/empaville_map.jpg'),true, (isset($param['mandatory']) &&  $param['mandatory'] == 1 ? 'true' : 'false'), isset($param['value'])? $param['value'] : null) !!}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            <!-- Parameters -->
                @foreach($parameters as $param)
                    {{--<div class="col-xs-12 parameter">--}}
                    @if($param["code"] == "dropdown" || $param['code'] == 'budget' || $param['code'] == 'category')

                        <div class="col-xs-12 col-sm-6 parameter">
                            {!! Form::oneSelect('parameter_'.$param['id'], $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'required':'notRequired' => 'required'] ) !!}
                        </div>
                    @elseif($param["code"] == "text")
                        <div class="col-xs-12 col-sm-6 parameter">
                            <div class="form-group {{($param['mandatory'] == 1)?'required':''}}">
                                {!! Form::oneText('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                            </div>
                        </div>
                    @elseif($param["code"] == "text_area")
                        <div class="col-xs-12 col-sm-6 parameter">
                            <div class="form-group {{($param['mandatory'] == 1)?'required':''}}">
                                {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                            </div>
                        </div>
                    @elseif($param['code'] == 'radio_buttons')
                        <div class="col-xs-12 col-sm-6 parameter">
                            <div class="form-group {{($param['mandatory'] == 1)?'required':''}}">
                                <label for="parameterRadio_{!! $param['id'] !!}"> {!! $param['name'] !!}</label>
                                @foreach($param['options'] as $key => $option)
                                    <div class="form-group">
                                        <input type="radio" name="parameter_{!! $param['id'] !!}" value="{!!$key !!}"
                                               {{($param['mandatory'] == 1)?'Required':''}}
                                               {{isset($topicParameters[$param['id']])? ($topicParameters[$param['id']]->pivot->value == $key ? 'checked' : '') : ''}}
                                               @if(ONE::actionType('topic') == 'show') disabled @endif><label> {!! $option !!}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($param['code'] == 'check_box')
                        <div class="col-xs-12 col-sm-6 parameter">
                            <div class="form-group checkbox-div {{($param['mandatory'] == 1)?'required':''}}" id="{!! $param['id'] !!}">
                                <label for="parameterRadio_{!! $param['id'] !!}"> {!! $param['name'] !!}</label>
                                @foreach($param['options'] as $key => $option)
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div style="float: left">
                                                <div class="checkboxPad">
                                                    <input id="checkboxOption{{$key}}" type="checkbox" name="parameter_{!! $param['id'] !!}[]" value="{!!$key !!}">
                                                    <label for="checkboxOption{{$key}}"></label>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="checkboxLabel">{!! $option !!}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{--</div>--}}
                @endforeach
                <!-- Files -->
                    @if(isset($configurations) && ((ONE::checkCBsOption($configurations, 'ALLOW-FILES')) || (ONE::checkCBsOption($configurations, 'ALLOW-PICTURES'))))
                        <div class="col-xs-12 parameter">
                            <div class="form-group" id="attachments-container">
                                {!! ONE::fileSimpleUploadBox("drop-zone", trans("empavillePadsIdea.drag_and_drop_files_to_here") , trans('empavillePadsIdea.add_files'), 'select-files', 'files-list', 'files') !!}
                            </div>
                        </div>
                    @endif


            </div>
            <div class="col-sx-12">
                {!! $form->make() !!}
            </div>
        </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
    <script>
        {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
        fileUploader.init();
        updateClickListener();
        updateFilesPostList('#files',1);
    </script>

    <script>
        $('#topic').submit(function() {
            var xpto = $('div.checkbox-div.required :checkbox:checked').length > 0;

            var divs = $('div.checkbox-div.required');
            var isValid = true;
            for (var i = 0; i < divs.length; i++) {
                if($('#' + divs[i].id +' :checkbox:checked').length <= 0){
                    $(divs[i]).addClass("checks-has-error");
                    isValid = false;
                }else{
                    $(divs[i]).removeClass("checks-has-error");
                }
            }
            if(!isValid){
                toastr.error('{{ trans('empavillePadsIdea.fillInAllRequiredFieldsOnForm') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('.oneFormSubmit').css('opacity','0.5');
            $('.oneFormSubmit').css('pointer-events','none');
        });
    </script>
    <style>
        #attachments-container div.row.box-header.with-border {

        }
    </style>
@endsection