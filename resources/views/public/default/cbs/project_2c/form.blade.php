@extends('public.default._layouts.index')

@section('header_scripts')
    <!-- Maps -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8&libraries=places" type="text/javascript"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="title margin-top-35">
                    <h2 class="bolder">{{trans("defaultPadsProject2C.create_project_2c")}}</h2>
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
                <div class="row margin-top">
                    <div class="col-xs-12">

                    {!! Form::oneText('title', trans('defaultPadsProject2C.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}

                    {!! Form::oneTextArea('summary', trans('defaultPadsProject2C.summary'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'summary', 'size' => '30x2', 'style' => 'resize: vertical', 'required' => 'required']) !!}

                    {!! Form::oneTextArea('contents', trans('defaultPadsProject2C.description'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x10', 'style' => 'resize: vertical', 'required' => 'required']) !!}

                    <!-- Files -->
                        @if(isset($configurations) && ((ONE::checkCBsOption($configurations, 'ALLOW-FILES')) || (ONE::checkCBsOption($configurations, 'ALLOW-PICTURES'))))
                            <div class="form-group margin-top">
                                {!! ONE::fileSimpleUploadBox("drop-zone", trans("defaultPadsProject2C.drag_and_drop_files_to_here") , trans('defaultPadsProject2C.add_files'), 'select-files', 'files-list', 'files') !!}
                            </div>
                        @endif
                    </div>

                    <!-- Parameters -->
                        @foreach($parameters as $param)
                            {{--<div class="col-xs-12 parameter">--}}
                            @if($param["code"] == "dropdown" || $param['code'] == 'budget')

                                {!! Form::oneSelect('parameter_'.$param['id'], $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'required':'notRequired' => 'required'] ) !!}

                            @elseif($param["code"] == "text")
                                <div class="form-group {{($param['mandatory'] == 1)?'required':''}}">
                                    {!! Form::oneText('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                </div>

                            @elseif($param["code"] == "text_area")

                                <div class="form-group {{($param['mandatory'] == 1)?'required':''}}">
                                    {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                </div>

                            @elseif($param['code'] == 'radio_buttons')

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

                            @elseif($param['code'] == 'check_box')

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

                            @endif
                            {{--</div>--}}
                        @endforeach
                    </div>
                    <!-- Form details -->
                    <div class="col-xs-12">
                        <!-- Google Maps -->
                    @if((count($parameters) > 0))
			<div class="row">
                        @foreach($parameters as $parameter)
                            @if($parameter["code"] == 'category')
				<div class="col-xs-12 col-md-6">
                                {!! Form::oneSelect('parameter_'.$parameter['id'], $parameter['name'], $parameter['options'], isset($topicParameters[$parameter['id']])? $topicParameters[$parameter['id']]->pivot->value : null, isset($topicParameters[$parameter['id']])? $parameter['id']['options'][$topicParameters[$parameter['id']]->pivot->value] : null, ['class' => 'form-control',($parameter['mandatory'] == 1)?'required':'notRequired' => 'required'] ) !!}
				</div>
			    @endif
                        @endforeach
                        @foreach($parameters as $parameter)
                            @if($parameter["code"] == 'google_maps')
				<div class="col-xs-12 col-md-6">
                                {!! Form::oneMaps('parameter_'.(($parameter["mandatory"]==1) ? "required_" : "").$parameter['id'],trans('defaultPadsProject2C.chose_location'),isset($parameter['value'])? $parameter['value'] : null,["required" => $parameter["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true]) !!}
				</div>
                            @endif
                        @endforeach
                        @foreach($parameters as $parameter)
                            @if($parameter["code"] == 'coin')
				<div class="col-xs-12 col-md-offset-6 col-md-6">
                                <div class="form-group {{($parameter['mandatory'] == 1)?'required':''}}">
                                    {!! Form::oneText('parameter_'.$parameter['id'], $parameter['name'], isset($topicParameters[$parameter['id']])? $topicParameters[$parameter['id']]->pivot->value : null, ['class' => 'form-control',($parameter['mandatory'] == 1)?'Required':'']) !!}
				</div>
				</div>
			    @endif
                        @endforeach
			</div>
                    @endif


                    <!-- Hidden fields -->
                    {!! Form::hidden('cb_key', isset($topic) ? $topic->id : $cbKey, ['id' => 'cb_key']) !!}
                    {!! Form::hidden('type', isset($type) ? $type : '', ['id' => 'type']) !!}



                </div>
            </div>
            {!! $form->make() !!}
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
                toastr.error('{{ trans('defaultPadsProject2C.fillInAllRequiredFieldsOnForm') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('.oneFormSubmit').css('opacity','0.5');
            $('.oneFormSubmit').css('pointer-events','none');
        });
    </script>
@endsection
