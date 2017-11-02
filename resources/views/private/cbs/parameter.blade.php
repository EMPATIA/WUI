@extends('private._private.index')

@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@section('content')
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb_title}}<br></p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
            <br>
        </p>
        @endif
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb_start_date}}</p>
    </div>
    <div class="margin-top-20">
        <div class="row">
            <div class="col-md-12">
                @if(isset($step))
                    @php $form = ONE::form('parameters', trans('privateParameter.details'))
                        ->settings(["model" => isset($parameter) ? $parameter : null,'id'=>isset($parameter) ? $parameter->id : null ])
                        ->show('CbsParametersController@edit', 'CbsParametersController@delete', ['type'=>$type,'cbKey' => $cbKey,'paramId' => isset($parameter) ? $parameter->id : null, 'step' => $step], 'CbsController@create', ['type'=>$type,'cbKey' => isset($cbKey) ? $cbKey : null, 'step' => $step])
                        ->create('CbsParametersController@store', 'CbsController@create', ['type'=>$type,'cbKey' => $cbKey, 'step' => $step])
                        ->edit('CbsParametersController@update', 'CbsParametersController@show', ['type'=>$type,'cbKey' => $cbKey,'id' => isset($parameter) ? $parameter->id : null])
                        ->open();

                    @endphp
                @else
                    @php $form = ONE::form('parameters', trans('privateParameter.details'))
                        ->settings(["model" => isset($parameter) ? $parameter : null,'id'=>isset($parameter) ? $parameter->id : null ])
                        ->show('CbsParametersController@edit', 'CbsParametersController@delete', ['type'=>$type,'cbKey' => $cbKey,'paramId' => isset($parameter) ? $parameter->id : null], 'CbsController@showParameters', ['type'=>$type,'id' => isset($cbKey) ? $cbKey : null])
                        ->create('CbsParametersController@store', 'CbsController@showParameters', ['type'=>$type,'cbKey' => $cbKey])
                        ->edit('CbsParametersController@update', 'CbsParametersController@show', ['type'=>$type,'cbKey' => $cbKey,'id' => isset($parameter) ? $parameter->id : null])
                        ->open();

                    @endphp
                @endif

                @if(ONE::actionType('parameters') != 'create')

                    {!! Form::hidden('file_id', isset($file) ? $file->id : null, ['id' => 'file_id']) !!}
                    {!! Form::hidden('parameterTypeId', isset($parameterType) ? $parameterType->id : null, ['id' => 'parameterTypeId']) !!}
                    {!! Form::hidden('parameterCode', isset($parameter) ? $parameter->code : null, ['id' => 'parameterCode']) !!}
                    {!! Form::oneText('parameterTypeName', array("name"=>trans('privateCbs.parameterTypeName'),"description"=>trans('privateCbs.parameterTypeNameDescription')), isset($parameterType) ? $parameterType->name : null, ['class' => 'form-control', 'id' => 'parameterTypeName' ,  'readonly']) !!}
                    {!! Form::oneCheckbox('parameterMandatory', trans('privateCbs.parameterMandatory'), 1,isset($parameter)? $parameter->mandatory : null, ['id' => 'parameterMandatory' ]) !!}
                    {!! Form::oneCheckbox('visible', trans('privateCbs.visible'), 1,isset($parameter)? $parameter->visible : null, ['id' => 'visible' ]) !!}
                    {!! Form::oneCheckbox('visibleInList', trans('privateCbs.visibleInList'), 1,isset($parameter)? $parameter->visible_in_list : null, ['id' => 'visibleInList' ]) !!}
                    {!! Form::oneCheckbox('use_filter', trans('privateCbs.parameterUseFilter'), 1,isset($parameter)? $parameter->use_filter : null, ['id' => 'use_filter' ]) !!}
                    {!! Form::oneCheckbox('private', trans('privateCbs.parameterIsPrivate'), 1,isset($parameter)? $parameter->private : null, ['id' => 'private' ]) !!}

                    {!! Form::oneText('parameter_code', array("name" => trans('privateCbs.parameterCodeName'),"description" => trans('privateCbs.parameterCodeDescription')), $parameter->parameter_code ?? null, ['class' => 'form-control', 'id' => 'parameterCodeName' , ONE::actionType('parameters') == 'show' ? 'readonly' : null]) !!}

                    <!-- Plupload Javascript fix and bootstrap fix @ start -->
                    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
                    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
                    <!-- Plupload Javascript fix and bootstrap fix @ End -->
                    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
                    <div class="row">
                        <div class="col-12">
                            @if(count($languages) > 0)
                                @foreach($languages as $languageItem)
                                    @php $form->openTabs('tab-translation' . $languageItem->code, $languageItem->name); @endphp

                                    <div style="">
                                        {!! Form::oneText('parameterName_'.$languageItem->code, array("name"=>trans('privateCbs.parameterName'),"description"=>trans('privateCbs.parameterNameDescription')),
                                              (property_exists($parameter->translations,$languageItem->code) ? $parameter->translations->{$languageItem->code}->parameter : null),
                                            ['class' => 'form-control', 'id' => 'parameterName_'.$languageItem->code, (isset($languageItem->default) && $languageItem->default == true ? 'required' : null)]) !!}
                                        {!! Form::oneTextArea('parameterDescription_'.$languageItem->code, array("name"=>trans('privateCbs.parameterDescription'),"description"=>trans('privateCbs.parameterDescriptionDescription')),
                                        (property_exists($parameter->translations,$languageItem->code) ? $parameter->translations->{$languageItem->code}->description : null),
                                        ['class' => 'form-control', 'id' => 'parameterDescription_'.$languageItem->code ,(isset($languageItem->default) && $languageItem->default == true ? 'required' : null)]) !!}

                                    </div>
                                    @if($parameterType->options == 1)
                                        <div class="card card-default">
                                            <div id="parameterOptionsDiv_{{$languageItem->code}}">
                                                <div class="card-body">
                                                    <div class="margin-bottom-20">
                                                        <label>
                                                            {!! trans("privateCbs.paramterOptions") !!}
                                                        </label>
                                                        @if(ONE::actionType('parameters') == 'edit')
                                                            <div class="pull-right">
                                                                <a class="btn btn-flat btn-create-inverted btn-sm" title=""
                                                                   data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div style="clear: both;"></div>


                                                    <div id="newOptionsDiv_{{$languageItem->code}}" class="newOptionsDiv {{(isset($languageItem->default) && $languageItem->default == true ? 'required' : null)}} row">
                                                        @foreach($parameter->options as $option)
                                                            <div class="col-md-3 optSelect_{!! $option->id !!}" id="optSelect_{!! $option->id !!}">
                                                                {{--                                                            {!! Form::hidden('optionsSelectIds[]', isset($option) ? $option->id : null, ['id' => 'optionsSelectIds']) !!}--}}
                                                                <div class="input-group margin-bottom-10">
                                                                {{-- {!! Form::oneText('optionsSelect['.$option->id.']['.$language->code.']','',
                                                                         (property_exists($option->translations,$language->code) ? $option->translations->{$language->code}->label : null) ,
                                                                          ['class' => 'form-control', 'id' => 'optionsSelect' , (isset($language->default) && $language->default == true ? 'required' : null)]) !!}--}}
                                                                        <input class="form-control" id="optionsSelect" {{ (isset($languageItem->default) && $languageItem->default == true ? 'required' : '') }}
                                                                               name="{{ 'optionsSelect['.$option->id.']['.$languageItem->code.']' }}" value="{{  (property_exists($option->translations,$languageItem->code) ? $option->translations->{$languageItem->code}->label : "")  }}" >
                                                                    @if(ONE::actionType('parameters') != 'show')
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

                                @endforeach
                                @php $form->makeTabs(); @endphp
                            @endif
                            @if(!empty($parameter->options))
                                @php $i=1; @endphp
                                <br>
                                @foreach($parameter->options as $option)
                                    @if(!empty($option->fields))
                                        <div class="top-2" id="accordion" role="tablist" aria-multiselectable="true">
                                            <div class="group_{!! $option->id !!}">
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="collapse-summary-title">
                                                        <h5>
                                                            <a role="button" data-toggle="collapse" data-parent="#collapse-{{$i}}" href="#collapse-{{$i}}" aria-expanded="true" aria-controls="collapse-{{$i}}" style="display:block;">
                                                                <i class="fa fa-chevron-down" aria-hidden="true" style="text-decoration: none!important;"></i>
                                                                @if(isset($option->translations->$language))
                                                                    {{$option->translations->$language->label}}
                                                                @endif
                                                            </a>
                                                        </h5>
                                                    </div>
                                                    <div id="collapse-{{$i}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-summary-title">
                                                        <div class="card-body">
                                                            {!! Form::oneText("option_code_".$i, trans('privateCbsParameters.code'), $option->code) !!}
                                                            @foreach($parameterType->param_add_fields as $fieldsType)
                                                                @php $wasAdded = false;@endphp
                                                                @foreach($option->fields as $fieldsOption)
                                                                    @if($fieldsType->code == 'color' && $fieldsOption->code == 'color')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneColor("option_color_".$i, trans('privateCbsParameters.color'), $fieldsOption->value) !!}
                                                                    @elseif($fieldsType->code == 'min_value' && $fieldsOption->code == 'min_value')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneText("option_min_value_".$i, trans('privateCbsParameters.min_value'), $fieldsOption->value) !!}
                                                                    @elseif($fieldsType->code == 'max_value' && $fieldsOption->code == 'max_value')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneText("option_max_value_".$i, trans('privateCbsParameters.max_value'), $fieldsOption->value) !!}
                                                                    @elseif($fieldsType->code == 'icon' && $fieldsOption->code == 'icon')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneFileUpload("option_icon_".$i, trans('privateCbsParameters.icon'), (!empty($fieldsOption->value) ? json_decode($fieldsOption->value) : []), $uploadKey) !!}
                                                                    @elseif($fieldsType->code == 'pin' && $fieldsOption->code == 'pin')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneFileUpload("option_pin_".$i, trans('privateCbsParameters.pin'), (!empty($fieldsOption->value) ? json_decode($fieldsOption->value) : []), $uploadKey) !!}
                                                                    @elseif($fieldsType->code == 'not_passed_translation' && $fieldsOption->code == 'not_passed_translation')
                                                                        @php $wasAdded = true;@endphp
                                                                        {!! Form::oneText("not_passed_translation".$i, trans('privateCbsParameters.not_passed_translation'), $fieldsOption->value) !!}
                                                                    @endif
                                                                @endforeach

                                                                @if (!$wasAdded)
                                                                    @if($fieldsType->code == 'color')
                                                                        {!! Form::oneColor("option_color_".$i, trans('privateCbsParameters.color'), $fieldsOption->value) !!}
                                                                    @elseif($fieldsType->code == 'min_value')
                                                                        {!! Form::oneText("option_min_value_".$i, trans('privateCbsParameters.min_value'), $fieldsOption->value) !!}
                                                                        @elseifif($fieldsType->code == 'max_value')
                                                                        {!! Form::oneText("option_max_value_".$i, trans('privateCbsParameters.max_value'), $fieldsOption->value) !!}
                                                                    @elseif($fieldsType->code == 'icon')
                                                                        {!! Form::oneFileUpload("option_icon_".$i, trans('privateCbsParameters.icon'), (!empty($fieldsOption->value) ? json_decode($fieldsOption->value) : []), $uploadKey) !!}
                                                                    @elseif($fieldsType->code == 'pin')
                                                                        {!! Form::oneFileUpload("option_pin_".$i, trans('privateCbsParameters.pin'), (!empty($fieldsOption->value) ? json_decode($fieldsOption->value) : []), $uploadKey) !!}
                                                                    @elseif($fieldsType->code == 'not_passed_translation')
                                                                        {!! Form::oneText("not_passed_translation".$i, trans('privateCbsParameters.not_passed_translation'), $fieldsOption->value) !!}

                                                                    @endif
                                                                @endif

                                                            @endforeach
                                                            {{--@endif--}}
                                                            @php $i++; @endphp
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @endforeach
                                <div class="top-2" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div id="newOptionFieldsDiv" class="newOptionFieldsDiv">
                                    </div>
                                </div>

                            @elseif(!empty($parameter->parameter_fields))
                                @foreach($parameter->parameter_fields as $fields)
                                    @if($fields->code == 'color')
                                        {!! Form::oneColor("color", trans('privateCbsParameters.color'), $fields->value) !!}
                                    @endif

                                    @if($fields->code == 'min_value')
                                        {!! Form::oneText("min_value", trans('privateCbsParameters.min_value'), $fields->value) !!}
                                    @endif

                                    @if($fields->code == 'max_value')
                                        {!! Form::oneText("max_value", trans('privateCbsParameters.max_value'), $fields->value) !!}
                                    @endif

                                    @if($fields->code == 'icon')
                                        {!! Form::oneFileUpload("icon", trans('privateCbsParameters.icon'), json_decode($fields->value), $uploadKey) !!}
                                    @endif

                                    @if($fields->code == 'pin')
                                        {!! Form::oneFileUpload("pin", trans('privateCbsParameters.pin'), json_decode($fields->value), $uploadKey) !!}
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
                @if(ONE::actionType('parameters') == 'create')
                    <div>
                        <div class="card flat">
                            <div class="card-header">
                                {!! trans("privateCbs.selectParameters") !!}
                            </div>
                            <div class="box-body">
                                {!! Form::hidden('_token',csrf_token(), ['id' => '_token']) !!}
                                {!! Form::hidden('file_id', isset($sponsor) ? $sponsor->file_id : null, ['id' => 'file_id']) !!}
                                <div id="param_list">
                                    @if((isset($parameters)?count($parameters):0)>0)
                                        <div class="form-group">
                                            <select class="form-control" id="paramSelect" name="paramSelect"
                                                    onchange="getParamOptions()" required>
                                                <option value="">{!! trans("privateCbs.selectOneParameter") !!}</option>
                                                @foreach($parameters as $param)
                                                    <option value="{{$param->id}}">{{$param->parameter}}</option>
                                                @endforeach
                                            </select>
                                            <!-- Multi-select parameter options -->
                                            <div class="btn-group" id="parameterOptions" style="width: 100%;">

                                            </div>
                                        </div>
                                        <div class="uploadImage" id="uploadImage" style="display: none">
                                            <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                                        </div>
                                    @else


                                        <div class="row">
                                            <div class="col-12 col-md-6 col-lg-7">
                                                <div style="display:flex;justify-content:center;align-items:center;padding:20px 0;height:100%;">
                                                    <!-- Templates -->
                                                    <div class="row" style="width: 100%">
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 text-left text-sm-left text-md-left text-lg-right">
                                                            <label for="title" style="margin-top:8px;white-space: nowrap;">{!! trans("parameter.template") !!}</label>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-5">
                                                            <select id="paramTemplateSelect" class="select2-default" id="paramTemplateSelect" name="paramTemplateSelect"
                                                                    onchange="selectNewTemplate(this.value)" style="width:100%;">
                                                                <option value="">{!! trans("privateCbs.selectOneParameterType") !!}</option>
                                                                @foreach($parameterTemplates as $template)
                                                                    <option value="{{$template->id}}">{{$template->parameter}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-3">
                                                            <a class="btn btn-success empatia btn-choose-templata" onclick="chooseTemplate()">{{ trans("cb.use") }}</a>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        function chooseTemplate(){
                                                            location.href='{!!  URL::action('CbsParametersController@create', ['type'=>$type,'cbKey'=>$cbKey]) !!}?template='+$('#paramTemplateSelect').val();
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-1 col-lg-1">
                                                <div style="display:flex;justify-content:center;align-items:center;height:100%">
                                                    <div style="padding: 28px 0;">
                                                        <i>{!! trans("privateCbs.or") !!}</i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-5 col-lg-4">

                                                <div style="display:flex;justify-content:center;align-items:center;padding:20px 0;height:100%">
                                                    <div>
                                                        <a href="javascript:addNewParameter()" class="btn btn-flat btn-preview" title=""
                                                           data-original-title="Create">
                                                            {!! trans("privateCbs.AddNewParameter") !!}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>










                                        {{--<div style="text-align: center;">
                                        <!-- <small>{!! trans("privateCbs.pleaseAddNewParameter") !!}</small> -->
                                            <br/>
                                            <a href="javascript:addNewParameter()" class="btn btn-flat btn-preview" title=""
                                               data-original-title="Create">
                                                {!! trans("privateCbs.AddNewParameter") !!}
                                            </a>
                                            <br>
                                            <br/>
                                            <i>{!! trans("privateCbs.or") !!}</i>
                                            <br/>
                                            <br/>
                                            <!-- Templates -->
                                            <div class="form-group">
                                                <label for="title">{!! trans("parameter.template") !!}</label>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="margin-bottom-20">
                                                            <select id="paramTemplateSelect" class="select2-default" id="paramTemplateSelect" name="paramTemplateSelect"
                                                                    onchange="selectNewTemplate(this.value)">
                                                                <option value="">{!! trans("privateCbs.selectOneParameterType") !!}</option>
                                                                @foreach($parameterTemplates as $template)
                                                                    <option value="{{$template->id}}">{{$template->parameter}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="btn btn-success empatia" onclick="chooseTemplate()">{{ trans("cb.use") }}</a>
                                                <script>
                                                    function chooseTemplate(){
                                                        location.href='{!!  URL::action('CbsParametersController@create', ['type'=>$type,'cbKey'=>$cbKey]) !!}?template='+$('#paramTemplateSelect').val();
                                                    }
                                                </script>
                                            </div>
                                        </div>--}}
                                    @endif
                                </div>
                                <div id="parameters_add">
                                    <div class="form-group">
                                        <label for="title">{!! trans("parameter.type") !!}</label>
                                        <div for="title" style="font-size:x-small">{{trans('parameter.typeDescription')}}</div>

                                        <select class="form-control" id="paramTypeSelect" name="paramTypeSelect"
                                                onchange="selectNewParameterType(this.value)" required>
                                            <option value="">{!! trans("privateCbs.selectOneParameterType") !!}</option>
                                            @foreach($parameterType as $typeParam)
                                                <option value="{{$typeParam->code}}">{{$typeParam->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="" id="parameter_name_div" hidden>
                                        <div class="form-group">
                                            {!! Form::oneCheckbox('mandatory', trans('privateCbs.parameterMandatory'), 1,!empty($parameterTemplateChoosed) ? $parameterTemplateChoosed->mandatory : null, ['id' => 'parameterMandatory' ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::oneCheckbox('visible', trans('privateCbs.visible'), 1,isset($parameterTemplateChoosed)? $parameterTemplateChoosed->visible : null, ['id' => 'visible' ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::oneCheckbox('visibleInList', trans('privateCbs.visibleInList'), 1,isset($parameterTemplateChoosed)? $parameterTemplateChoosed->visible_in_list : null, ['id' => 'visibleInList' ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::oneCheckbox('use_filter', trans('privateCbs.parameterUseFilter'), 1,!empty($parameterTemplateChoosed) ? $parameterTemplateChoosed->use_filter : null, ['id' => 'use_filter' ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::oneCheckbox('private', trans('privateCbs.parameterIsPrivate'), 1,!empty($parameterTemplateChoosed) ? $parameterTemplateChoosed->private : null, ['id' => 'private' ]) !!}
                                        </div>

                                        {{--Insert Parameter Code Here--}}
                                        <div class="form-group">
                                            {!! Form::oneText("parameter_code", trans('privateCbsParameters.parameter_code')) !!}
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                @if(count($languages) > 0)
                                                    @foreach($languages as $languageItem)
                                                        @php $form->openTabs('tab-translation' . $languageItem->code, $languageItem->name); @endphp

                                                        <div>
                                                            {!! Form::oneText('parameterName_'.$languageItem->code, array("name"=>trans('privateCbs.parameterName'),"description"=>trans('privateCbs.parameterNameDescription')), null, ['class' => 'form-control', 'id' => 'parameterName_'.$languageItem->code,
                                                                (isset($languageItem->default) && $languageItem->default == true ? 'required' : null)]) !!}
                                                            {!! Form::oneTextArea('parameterDescription_'.$languageItem->code, array("name"=>trans('privateCbs.parameterDescription'),"description"=>trans('privateCbs.parameterDescriptionDescription')), null, ['class' => 'form-control', 'id' => 'parameterDescription_'.$languageItem->code ,
                                                              (isset($languageItem->default) && $languageItem->default == true ? 'required' : null)]) !!}
                                                        </div>

                                                        <div id="parameterOptionsDiv_{{$languageItem->code}}" class="card flat parameterOptionsDiv" hidden>
                                                            <div class="card-header">
                                                                {!! trans("privateCbs.options") !!}
                                                                <div class="pull-right">
                                                                    <a class="btn btn-flat btn-create-inverted btn-sm" title=""
                                                                       data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="box-body">
                                                                <div id="newOptionsDiv_{{$languageItem->code}}" class="row newOptionsDiv {{(isset($languageItem->default) && $languageItem->default == true ? 'required' : null)}}">

                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endforeach
                                                    @php $form->makeTabs(); @endphp
                                                @endif
                                            </div>
                                        </div>
                                        <div class=" top-2" id="accordion" role="tablist" aria-multiselectable="true">
                                            <div id="newOptionFieldsDiv" class="newOptionFieldsDiv">

                                            </div>
                                        </div>
                                        <div class="card fields" id="fields">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" hidden id="parameter_minMaxChars_div">
                                    <label for="title">{!! trans("cbs.parameterMinChars") !!}</label>
                                    <input class="form-control" type="number" name="parameterMinChars" id="parameterMinChars" min="3" max="20" value="3">
                                    <label for="title">{!! trans("cbs.parameterMaxChars") !!}</label>
                                    <input class="form-control" type="number" name="parameterMaxChars" id="parameterMaxChars" min="20" max="100" value="20">
                                </div>

                                <div id="imageMapGroup" class="form-group" style="display:none;">
                                    <label for="parameterDescription">{{ trans('privateCbs.imageMap') }}</label>
                                    <br/>
                                    <div style="width:100%;border:1px solid #d2d6de;padding:10px;text-align:center;">
                                        <img id="imageMapFile" class="img" src="{{ asset(ONE::getEmpavilleImageMap()) }}" id="imageMapFile" style="width:200px;max-height:400px;"/>
                                    </div>
                                </div>

                                <div id="imageMapEmpavilleParkGroup" class="form-group" style="display:none;">
                                    <label for="parameterDescription">{{ trans('privateCbs.imageMap') }}</label>
                                    <br/>
                                    <div style="width:100%;border:1px solid #d2d6de;padding:10px;text-align:center;">
                                        <img id="imageMapFile" class="img" src="{{ asset(ONE::getEmpavilleParkImageMap()) }}" id="imageMapFile" style="width:200px;max-height:400px;"/>
                                    </div>
                                </div>


                                <div class="uploadImage" id="uploadImageCb">
                                    <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                                </div>

                            </div>
                        </div>
                    </div>


                @endif
                {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('files.resize')) !!}

                {!! $form->make() !!}
            </div>
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
    <!-- Helper Functions -->
    <script>

        $( document ).ready(function() {
            setTimeout(function(){

                $('#parameters_add').css('display','none');
                $('#uploadImageCb').css('display','none');

            }, 1);
        });
        @if((ONE::actionType('parameters') == 'create') and (isset($parameters)?count($parameters):0)==0)
           $("[type=submit]").hide();
        @endif

    </script>
    <script>

        function addNewParameter() {
            $("#param_list").hide();
            $("#button_add").hide();
            $("#parameters_add").show();
            $("[type=submit]").show();

            @if(!empty($parameterTemplateChoosed))
             setTimeout(
                function(){
                    @if( !empty($parameterTemplateChoosed->type->code) )
                        $('#paramTypeSelect option[value={{ $parameterTemplateChoosed->type->code }}]').prop('selected', true);
                    selectNewParameterType("{{ $parameterTemplateChoosed->type->code }}");
                    @foreach($parameterTemplateChoosed->template_options as $opt)
addNewOption("{{ $opt->label }}");
                    @endforeach
                    @endif
                }
                , 1);
            @endif
        }

        function selectNewParameterType(id) {
            $(".fields").empty();
            $(".fields").hide();
            $('.newOptionFieldsDiv').empty();
            $('.newOptionFieldsDiv').hide();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('CbsParametersController@getParameterType', ['type'=>$type,'cbKey' => $cbKey])}}', // This is the url we gave in the route
                data: {code : id}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $("#parameter_name_div").show();
                    console.log(response.parameterType);
                    $(".parameterOptionsDiv").hide();
                    $("#uploadImageCb").hide();
                    $("#imageMapGroup").hide();
                    $("#imageMapEmpavilleParkGroup").hide();
                    $("#newOptionsDiv").empty();


                    if(response.parameterType.options == 1){

                        $(".parameterOptionsDiv").show();
//                        $("#parameter_minMaxChars_div").hide();
                        $("#uploadImageCb").hide();
                        $("#imageMapGroup").hide();
                        $("#imageMapEmpavilleParkGroup").hide();
                        $('.fields').empty();

                        if(numInputs == 0){
                            addNewOption();
                        }
                    }else{
                        $('.newOptionsDiv').empty();
                        if(response.parameterType.param_add_fields.length > 0){
                            console.log("maior");
                            $.ajax({
                                'url' : '{{action('CbsParametersController@getNewFields', ['type'=>$type,'cbKey' => $cbKey,'paramId' => isset($parameter) ? $parameter->id : null])}}',
                                'method' : 'get',
                                'data' : {uploadKey : '{{ $uploadKey }}'},
                                success: function(response){
                                    $('.fields').show();
                                    $(response).appendTo($('.fields'))
                                },
                                error: function(){
                                    console.log("Erro!")
                                }
                            })
                        }
                    }

                    if(response.parameterType.code == 'text' || response.parameterType.code == 'text_area'){

                        $(".parameterOptionsDiv").hide();
                        $("#uploadImageCb").hide();
                        $("#imageMapGroup").hide();
                        $("#imageMapEmpavilleParkGroup").hide();
                        // $("#newOptionsDiv").empty();
                    }

                    if(response.parameterType.code == 'image_map'){
                        $("#uploadImageCb").hide();
//                        $("#parameter_minMaxChars_div").hide();
                        $(".parameterOptionsDiv").hide();
                        // $("#newOptionsDiv").empty();
                        $("#imageMapGroup").show();
                        $("#imageMapEmpavilleParkGroup").hide();
                    }

                    if(response.parameterType.code == 'empaville_park_map'){
                        $("#uploadImageCb").hide();
//                        $("#parameter_minMaxChars_div").hide();
                        $(".parameterOptionsDiv").hide();
                        $("#imageMapGroup").hide();
                        // $("#newOptionsDiv").empty();
                        $("#imageMapEmpavilleParkGroup").show();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            })
            //TODO:add and remove required attribute from options
            var numInputs = $('.newOptionsDiv :input').size();

            {{--if (id != '') {--}}
            {{--console.log(id);--}}
            {{--switch (id) {--}}
            {{--case 'text':--}}
            {{--case 'text_area':--}}
            {{--//                        $("#parameter_minMaxChars_div").show();--}}
            {{--$(".parameterOptionsDiv").hide();--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--$("#imageMapGroup").hide();--}}
            {{--$("#imageMapEmpavilleParkGroup").hide();--}}
            {{--// $("#newOptionsDiv").empty();--}}
            {{--break;--}}
            {{--case 'category':--}}
            {{--case 'budget':--}}
            {{--case 'radio_buttons':--}}
            {{--case 'check_box':--}}
            {{--case 'dropdown':--}}
            {{--$(".parameterOptionsDiv").show();--}}
            {{--//                        $("#parameter_minMaxChars_div").hide();--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--$("#imageMapGroup").hide();--}}
            {{--$("#imageMapEmpavilleParkGroup").hide();--}}
            {{--//$("#newOptionsDiv").empty();--}}
            {{--@if(empty($parameterTemplateChoosed))--}}
            {{--if(numInputs == 0){--}}
            {{--addNewOption();--}}
            {{--}--}}
            {{--@endif--}}
            {{--break;--}}
            {{--case 'image_map':--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--//                        $("#parameter_minMaxChars_div").hide();--}}
            {{--$(".parameterOptionsDiv").hide();--}}
            {{--// $("#newOptionsDiv").empty();--}}
            {{--$("#imageMapGroup").show();--}}
            {{--$("#imageMapEmpavilleParkGroup").hide();--}}
            {{--break;--}}
            {{--case 'empaville_park_map':--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--//                        $("#parameter_minMaxChars_div").hide();--}}
            {{--$(".parameterOptionsDiv").hide();--}}
            {{--$("#imageMapGroup").hide();--}}
            {{--// $("#newOptionsDiv").empty();--}}
            {{--$("#imageMapEmpavilleParkGroup").show();--}}
            {{--break;--}}
            {{--default:--}}
            {{--//                        $("#parameter_minMaxChars_div").hide();--}}
            {{--$(".parameterOptionsDiv").hide();--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--$("#imageMapGroup").hide();--}}
            {{--$("#imageMapEmpavilleParkGroup").hide();--}}
            {{--//$("#newOptionsDiv").empty();--}}
            {{--break;--}}
            {{--}--}}
            {{--$("#parameter_name_div").show();--}}
            {{--}--}}
            {{--else{--}}
            {{--$("#parameter_name_div").hide();--}}
            {{--//                $("#parameter_minMaxChars_div").hide();--}}
            {{--$(".parameterOptionsDiv").hide();--}}
            {{--$("#uploadImageCb").hide();--}}
            {{--}--}}
        }


        function getParamOptions() {

            var idParam = $('#paramSelect').val();


            if (idParam == "") {
                $('#parameterOptions').html("");
                $("#uploadImage").css('visibility', 'hidden');
            } else {
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '{{action('CbsParametersController@getParameterOptions')}}', // This is the url we gave in the route
                    data: {postId: idParam, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                    success: function (response) { // What to do if we succeed

                        $("#parameterOptions").html(response);
                        var $resultImage = $(response).filter('#imageMap');
                        var $resultText = $(response).filter('#textInput');

                        $("#uploadImage").css('visibility', 'hidden');
                        if ($resultImage.length > 0) {
                            $("#uploadImage").css('visibility', 'visible');
                        } else if ($resultText.length > 0) {

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        function saveNewOption() {

            var newOption = $("#btnOption").val();
            if (newOption.length > 0) {
                var idParam = $('#paramSelect').val();

                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '{{action('CbsParametersController@addParameterOptions')}}', // This is the url we gave in the route
                    data: {idParam: idParam, label: newOption, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                    success: function (object) { // What to do if we succees

                        $("#btnOption").val('');
                        $('#optionSelect').append($('<option>', {
                            value: object[0].id,
                            text: object[0].label
                        }));

                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            } else {
                alert('Please enter a valid name!');
            }
        }
        var optionIndex = 1;
        var optionField = 0;
        var optionsDiv = $('.newOptionsDiv');
        if(optionsDiv.length > 0){
            optionIndex = $('#'+optionsDiv[0].id +' > div').size() + 1;
        }

        function addNewOption(val) {
            //Default Values
            val = typeof(val) != 'undefined' ? val : '';
            var text = $('#opt_'+val).find('input').val();
//                    console.log(text);
            var newOptionsDiv = $('.newOptionsDiv');
            for(var i=0 ;i< newOptionsDiv.length; i++){
                var optDivId = newOptionsDiv[i].id;
                var required = "notRequired";
                var optRequired = $('#'+newOptionsDiv[i].id).hasClass( "required" );
                if(optRequired){
                    required = "required";
                }
                var lang = optDivId.replace('newOptionsDiv_','');
                var html = '';
                html +='<div class="col-md-3 opt_'+optionIndex+' " id="opt_'+optionIndex+'"><div class="input-group margin-bottom-10"><input class="form-control" onchange="changePlaceHolderText('+optionIndex+')" id="optionsNew" placeholder="{!! trans("privateCbs.optionValue") !!}" value="'+val+'" '+required+' name="optionsNew['+optionIndex+']['+lang+']" type="text">';
                html +='<a class="btn btn-flat btn-danger btn-sm" onclick="removeOption('+optionIndex+')" data-original-title="Delete"><i class="fa fa-remove"></i></a></div>';
                html +='</div>';
                $(html).appendTo(newOptionsDiv[i]);
            }

            addOptionFieldDiv();

            optionIndex++;
            optionField++;

        }


        function removeOption(id){
            console.log("remove option");
            var numInputs = 0;
            var optionsDiv = $('.newOptionsDiv');
            var optionFieldsDiv = $('.newOptionFieldsDiv');
            if(optionsDiv.length > 0){
                numInputs = $('#'+optionsDiv[0].id +' :input').size();
            }
            if(numInputs <2){
                toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('.opt_'+id).remove();
            console.log($('.group_'+id))
            $('.group_'+id).remove();
            return true;
        }
        function removeOptionSelect(id){
            console.log("remove option");
            var numInputs = 0;
            var optionsDiv = $('.newOptionsDiv');
            if(optionsDiv.length > 0){
                numInputs = $('#'+optionsDiv[0].id +' :input').size();
            }
            if(numInputs <2){
                toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('.optSelect_'+id).remove();
            console.log($('.group_'+id))
            $('.group_'+id).remove();
            return true;
        }

        @if(isset($templateId) && $templateId!="")
        $( document ).ready(function() {
            setTimeout(addNewParameter, 1);
        });
        @endif

        function addOptionFieldDiv(){
            $.ajax({
                'url' : '{{action('CbsParametersController@getNewOptionFields', ['type'=>$type,'cbKey' => $cbKey,'paramId' => isset($parameter) ? $parameter->id : null])}}',
                'method' : 'get',
                'data' : {uploadKey : '{{ $uploadKey }}', optionField: optionIndex },
                success: function(response){
                    if(response == ''){

                    }else{
                        $('.newOptionFieldsDiv').show();
                        $(response).appendTo($('.newOptionFieldsDiv'))
                    }
                },
                error: function(){
                    console.log("Erro!")
                }
            })
        }



        //        Function to change placeholder of inputs text options

        function changePlaceHolderText(id){
            var parentDiv = $('#opt_'+id).closest('.newOptionsDiv');

            if ($(parentDiv).hasClass('required')) {
                var text = $('#opt_'+id).find('input').val();
                $("#opt_"+id+' card-header').text(text)

                $('.opt_' + id).each(function () {
                    console.log(this)
                    if(text != ''){
                        $('.title_' + id).text(text);
                        $(this).find('input').attr('value', text);
                    }else{
                        $(this).find('input').attr('value', '{!! trans("privateCbs.optionValue") !!}');
                    }
                });
            }



        }

    </script>
@endsection
