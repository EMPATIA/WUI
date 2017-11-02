@extends('private._private.index')

@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('parametersTemplate', trans('privateParameterTemplates.details'), 'cb', 'parameter_template')
                    ->settings(["model" => isset($parameterTemplate) ? $parameterTemplate : null,'id'=>isset($parameterTemplate) ? $parameterTemplate->parameter_template_key : null ])
                    ->show('ParametersTemplateController@edit', 'ParametersTemplateController@delete', ['key' => isset($parameterTemplate) ? $parameterTemplate->parameter_template_key : null], null)
                    ->create('ParametersTemplateController@store', 'ParametersTemplateController@index', ['key' => isset($parameterTemplate) ? $parameterTemplate->parameter_template_key : null])
                    ->edit('ParametersTemplateController@update', 'ParametersTemplateController@show', ['key' => isset($parameterTemplate) ? $parameterTemplate->parameter_template_key : null])
                    ->open();
            @endphp
            @if(ONE::actionType('parametersTemplate') != 'create')
                {!! Form::hidden('file_id', isset($file) ? $file->id : null, ['id' => 'file_id']) !!}
                {!! Form::hidden('paramTypeSelect', isset($parameterType) ? $parameterType->id : null, ['id' => 'paramTypeSelect']) !!}
                {!! Form::hidden('parameterCode', isset($parameterType) ? $parameterType->code : null, ['id' => 'parameterCode']) !!}
                {!! Form::oneText('parameterTypeName', trans('privateCbs.parameterTypeName'), isset($parameterType) ? $parameterType->name : null, ['class' => 'form-control', 'id' => 'parameterTypeName' ,  'readonly']) !!}
                {!! Form::oneText('parameterName', trans('privateCbs.parameterName'), isset($parameterTemplate) ? $parameterTemplate->parameter : null, ['class' => 'form-control', 'id' => 'parameterName' ,  'required']) !!}
                {!! Form::oneTextArea('parameterDescription', trans('privateCbs.parameterDescription'), isset($parameterTemplate) ? $parameterTemplate->description : null, ['class' => 'form-control', 'id' => 'parameterDescription' ,  'required']) !!}
                {!! Form::oneCheckbox('parameterMandatory', trans('privateCbs.parameterMandatory'), 1,isset($parameterTemplate)? $parameterTemplate->mandatory : null, ['id' => 'parameterMandatory' ]) !!}
                {!! Form::oneCheckbox('visible', trans('privateCbs.visible'), 1,isset($parameterTemplate)? $parameterTemplate->visible : null, ['id' => 'visible' ]) !!}
                {!! Form::oneCheckbox('visibleInList', trans('privateCbs.visibleInList'), 1,isset($parameterTemplate)? $parameterTemplate->visible_in_list : null, ['id' => 'visibleInList' ]) !!}
                {!! Form::oneCheckbox('use_filter', trans('privateCbs.parameterUseFilter'), 1,isset($parameterTemplate)? $parameterTemplate->use_filter : null, ['id' => 'use_filter' ]) !!}

                @if(isset($parameterTemplate->template_options)? (count($parameterTemplate->template_options)> 0 ? true:false):false)
                    <div class="box">
                        <div class="card flat">
                            <div class="card-header">
                                {!! trans("privateCbs.paramterOptions") !!}
                                @if(ONE::actionType('parametersTemplate') == 'edit')
                                    <div class="pull-right">
                                        <a class="btn btn-flat btn-success btn-sm" title=""
                                           data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="box-body">
                                <div id="newOptionsDiv">
                                    @foreach($parameterTemplate->template_options as $option)
                                        <div class="col-md-3" id="optSelect_{!! $option->id !!}">
                                            {!! Form::hidden('optionsSelectIds[]', isset($option) ? $option->id : null, ['id' => 'optionsSelectIds']) !!}
                                            <div class="btn-group">
                                                {!! Form::oneText('optionsSelect[]','', $option->label , ['class' => 'form-control', 'id' => 'optionsSelect' ,  'required']) !!}
                                            </div>
                                            <div class="btn-group">
                                                @if(ONE::actionType('parametersTemplate') != 'show')
                                                    <a class="btn btn-flat btn-danger btn-sm" onclick="removeOptionSelect('{!! $option->id !!}')" data-original-title="Delete"><i class="fa fa-remove"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(ONE::actionType('parametersTemplate') == 'show' and isset($parameterType)?($parameterType->code == 'image_map'):false and !empty($file))
                    <div class="box-body">
                        <div class="col-3">
                            <img class="img" src="{{action('FilesController@download', ['id'=>$file->id,'code'=>$file->code] )}}"  id="imageMapparameter">
                        </div>
                    </div>
                @endif
                @if(ONE::actionType('parametersTemplate') == 'edit' and $parameterType->code == 'image_map')
                    <div class="uploadImage" id="uploadImage">
                        <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                    </div>
                @endif
            @endif

            @if(ONE::actionType('parametersTemplate') == 'create')
                <div class="box">
                    <div class="card flat">
                        <div class="box-body">
                            {!! Form::hidden('_token',csrf_token(), ['id' => '_token']) !!}
                            {!! Form::hidden('file_id', isset($parameterTemplate) ? $parameterTemplate->file_id : null, ['id' => 'file_id']) !!}
                            {!! Form::hidden('file_code', isset($parameterTemplate) ? $parameterTemplate->file_code : null, ['id' => 'file_code']) !!}
                            <div id="parameters_add">
                                <div class="form-group">
                                    <label for="title">{!! trans("parameter.type") !!}</label>
                                    <select class="form-control" id="paramTypeSelect" name="paramTypeSelect"
                                            onchange="selectNewParameterType(this.value)" required>
                                        <option value="">{!! trans("privateCbs.selectOneParameterType") !!}</option>
                                        @foreach($parameterType as $type)
                                            <option value="{{$type->code}}">{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="parameter_name_div" hidden>
                                    <div class="form-group">
                                        {!! Form::oneCheckbox('mandatory', trans('privateCbs.parameterMandatory'), 1,null, ['id' => 'parameterMandatory' ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::oneCheckbox('visible', trans('privateCbs.visible'), 1,isset($parameterTemplate)? $parameterTemplate->visible : null, ['id' => 'visible' ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::oneCheckbox('visibleInList', trans('privateCbs.visibleInList'), 1,isset($parameterTemplate)? $parameterTemplate->visible_in_list : null, ['id' => 'visibleInList' ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::oneCheckbox('use_filter', trans('privateCbs.parameterUseFilter'), 1,isset($parameterTemplate)? $parameterTemplate->use_filter : null, ['id' => 'use_filter' ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::oneText('parameterName', trans('privateCbs.parameterName'),null, ['class' => 'form-control', 'id' => 'parameterName','required']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::oneTextArea('parameterDescription', trans('privateCbs.parameterDescription'),null, ['class' => 'form-control', 'id' => 'parameterDescription' ,  'required']) !!}
                                    </div>
                                </div>
                                <div class="form-group" hidden id="parameter_minMaxChars_div">
                                    <label for="title">{!! trans("cbs.parameterMinChars") !!}</label>
                                    <input class="form-control" type="number" name="parameterMinChars" id="parameterMinChars" min="3" max="20" value="3">
                                    <label for="title">{!! trans("cbs.parameterMaxChars") !!}</label>
                                    <input class="form-control" type="number" name="parameterMaxChars" id="parameterMaxChars" min="20" max="100" value="20">
                                </div>
                                <div id="parameterOptionsDiv" class="card flat" hidden>
                                    <div class="card-header">
                                        Options
                                        <div class="pull-right">
                                            <a class="btn btn-flat btn-success btn-sm" title=""
                                               data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div id="newOptionsDiv">

                                        </div>
                                    </div>
                                </div>
                                <div class="uploadImage" id="uploadImageCb">
                                    <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('files.resize')) !!}

            {!! $form->make() !!}
        </div>

    </div>
@endsection
@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    @include('private._private.functions')  Helper Functions
    <script>
        $( document ).ready(function() {
            setTimeout(function(){
                $('#uploadImageCb').css('display','none');
            }, 1);
        });

    </script>
    <script>

        {!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'imageMapUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();
        updateClickListener();


        function selectNewParameterType(id) {
            //TODO:add and remove required attribute from options
            var numInputs = $('#newOptionsDiv :input').size();

            if (id != '') {
                switch (id) {
                    case 'text':
                    case 'text_area':
                        $("#parameter_minMaxChars_div").show();
                        $("#parameterOptionsDiv").hide();
                        $("#uploadImageCb").hide();
                        $("#newOptionsDiv").empty();
                        break;
                    case 'category':
                    case 'budget':
                    case 'radio_buttons':
                    case 'check_box':
                    case 'dropdown':
                        $("#parameterOptionsDiv").show();
                        $("#parameter_minMaxChars_div").hide();
                        $("#uploadImageCb").hide();
                        $("#newOptionsDiv").empty();
                        if(numInputs == 0){
                            addNewOption();
                        }
                        break;
                    case 'image_map':
                        $("#uploadImageCb").show();
                        $("#parameter_minMaxChars_div").hide();
                        $("#parameterOptionsDiv").hide();
                        $("#newOptionsDiv").empty();
                        break;
                    default:
                        $("#parameter_minMaxChars_div").hide();
                        $("#parameterOptionsDiv").hide();
                        $("#uploadImageCb").hide();
                        $("#newOptionsDiv").empty();
                        break;
                }
                $("#parameter_name_div").show();
            }
            else{
                $("#parameter_name_div").hide();
                $("#parameter_minMaxChars_div").hide();
                $("#parameterOptionsDiv").hide();
                $("#uploadImageCb").hide();
            }
        }

        var i = $('#newOptionsDiv').size() + 1;

        function addNewOption(val) {
            //Default Values
            val = typeof(val) != 'undefined' ? val : '';

            var newOptionsDiv = $('#newOptionsDiv');
            var html = '';
            html +='<div class="col-md-3" id="opt_'+i+'"><div class="btn-group"><input class="form-control" id="optionsNew" required="required" placeholder="Option Value" value="'+val+'" required="required" name="optionsNew[]" type="text"></div>';
            html +='<div class="btn-group"><a class="btn btn-flat btn-danger btn-sm" onclick="removeOption('+i+')" data-original-title="Delete"><i class="fa fa-remove"></i></a></div>';
            $(html).appendTo(newOptionsDiv);
            i++;
        }

        function removeOption(id){
            var numInputs = $('#newOptionsDiv :input').size();
            if(numInputs <2){
                toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('#opt_'+id).remove();
            return true;
        }
        function removeOptionSelect(id){
            var numInputs = $('#newOptionsDiv :input').size();
            if(numInputs <4){
                toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('#optSelect_'+id).remove();
            return true;
        }
    </script>
@endsection