@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('parameterUserTypes', trans('privateParameterUserTypes.details'), 'auth', 'user_parameters')
        ->settings(["model" => isset($parameterUserType) ? $parameterUserType : null,'id'=>isset($parameterUserType) ? $parameterUserType->parameter_user_type_key : null ])
        ->show('ParameterUserTypesController@edit', 'ParameterUserTypesController@delete', ['key' => isset($parameterUserType) ? $parameterUserType->parameter_user_type_key : null], 'ParameterUserTypesController@index')
        ->create('ParameterUserTypesController@store', 'ParameterUserTypesController@index', ['key' => isset($parameterUserType) ? $parameterUserType->parameter_user_type_key : null])
        ->edit('ParameterUserTypesController@update', 'ParameterUserTypesController@show', ['key' => isset($parameterUserType) ? $parameterUserType->parameter_user_type_key : null])
        ->open();
    @endphp
    {!! Form::hidden('parameter_type_code', isset($selectedType) ? $selectedType : null, ['id' => 'parameter_type_code']) !!}
    {!! Form::oneSelect('parameter_type_code', trans('privateParameters.type'),!empty($types) ? $types : null, isset($selectedType) ? $selectedType : "" , isset($typeName) ? $typeName : null,
                        ['class' => 'form-control', 'id' => 'parameter_type_code','onchange'=> 'selectNewParameterType(this.value)',(ONE::actionType('parameterUserTypes') != 'create' ? 'disabled' : null)] ) !!}

    {!! Form::oneText('code', trans('privateParameters.code'), isset($parameter) ? $parameterUserType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    @if(ONE::actionType('parameterUserTypes') == 'show')
        {!! Form::oneText('name', trans('privateParameters.name'), isset($parameter) ? $parameterUserType->name : null, ['class' => 'form-control', 'id' => 'parameter']) !!}
    @endif

    {!! Form::oneSwitch('parameterMandatory',trans('privateParameterUserTypes.parameter_mandatory'), isset($parameterUserType)? $parameterUserType->mandatory : null,
        ["readonly"=>((ONE::actionType('parameterUserTypes') == 'show') ? true : false),'id' => 'parameterMandatory'] ) !!}

    {!! Form::oneSwitch('parameterUnique',trans('privateParameterUserTypes.parameter_unique'), isset($parameterUserType)? $parameterUserType->parameter_unique : null,
            ["readonly"=>((ONE::actionType('parameterUserTypes') == 'show') ? true : false),'id' => 'parameterUnique'] ) !!}


    <hr style="margin: 10px 0 10px 0">
    @if( !empty($languages) && count($languages) > 0)
        <div class="row">
            <div class="col-12">
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        <!-- Name -->
                        {!! Form::oneText($language->default == true ? 'required_name_'.$language->code:'name_'.$language->code,
                                          trans('privateParameters.name'),
                                          !empty($translations[$language->code]->name) ? $translations[$language->code]->name : null,
                                          ['class' => 'form-control', 'id' => 'name_'.$language->code ]) !!}

                        {!! Form::oneTextArea('description_'.$language->code,
                                          trans('privateParameters.description'),
                                          !empty($translations[$language->code]->description) ? $translations[$language->code]->description : null,
                                          ['class' => 'form-control', 'id' => 'description_'.$language->code ]) !!}
                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            </div>
        </div>
    @endif
    @if(ONE::actionType('parameterUserTypes') != 'create')
        @if(isset($parameterUserType->parameter_user_options) && count($parameterUserType->parameter_user_options) > 0)
            <div class="box" id="parameterOptionsDiv">
                <div class="card flat">
                    <div class="card-header">
                        {!! trans("privateParameters.parameterOptions") !!}
                        @if(ONE::actionType('parameterUserTypes') == 'edit')
                            <div class="pull-right">
                                <a class="btn btn-flat btn-success btn-sm" title=""
                                   data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="box-body">
                        @if( !empty($languages) && count($languages) > 0)
                            <div class="row">
                                <div class="col-12">
                                    @foreach($languages as $language)
                                        @php $form->openTabs('tab-translation-options-' . $language->code, $language->name); @endphp
                                        <div style="padding:10px;" id="newOptionsDiv_{{$language->code}}">
                                            @foreach($optionsTranslations as $key => $optTrans)
                                                <div class="col-md-3" id="opt_{!! $key !!}_{!! $language->code !!}">
                                                    <div class="btn-group">
                                                        {!! Form::oneText('optionsSelect['.$key.']['.$language->code.']','', isset($optTrans[$language->code]) ? $optTrans[$language->code]->name : '' , ['class' => 'form-control', 'id' => 'optionsSelect' , $language->default == true ? 'required' : null]) !!}
                                                    </div>
                                                    <div class="btn-group">
                                                        @if(ONE::actionType('parameterUserTypes') != 'show')
                                                            <a class="btn btn-flat btn-danger btn-sm" onclick="removeOption('{!! $key !!}')" data-original-title="Delete"><i class="fa fa-remove"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                    @php $form->makeTabs(); @endphp
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    @else
        <div class="box" id="parameterOptionsDiv"  hidden>
            <div class="card flat">
                <div class="card-header">
                    {!! trans("privateParameters.parameterOptions") !!}
                    <div class="pull-right">
                        <a class="btn btn-flat btn-success btn-sm" title=""
                           data-original-title="Create" id="buttonAddOption" onclick="addNewOption()">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    @if( !empty($languages) && count($languages) > 0)
                        <div class="row">
                            <div class="col-12">
                                @foreach($languages as $language)
                                    @php $form->openTabs('tab-translation-options-' . $language->code, $language->name); @endphp
                                    <div style="padding:10px;" id="newOptionsDiv_{{$language->code}}">

                                    </div>
                                @endforeach
                                @php $form->makeTabs(); @endphp
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {!! $form->make() !!}
@endsection
@section('scripts')
    <script>
        function selectNewParameterType(id) {
            //TODO:add and remove required attribute from options
            var numInputs = $('#newOptionsDiv :input').size();
            if (id != '') {
                switch (id) {
                    case 'category':
                    case 'budget':
                    case 'radio_buttons':
                    case 'check_box':
                    case 'dropdown':
                    case 'gender':
                        $("#parameterOptionsDiv").show();
                        addNewOption();
                        break;
                    default:
                        $("#parameterOptionsDiv").hide();
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

        function addNewOption(val) {
            //Default Values
            val = typeof(val) != 'undefined' ? val : '';
                    @foreach($languages as $language)
            var i = $('#newOptionsDiv_{{$language->code}} :input').size()+1;
            var newOptionsDiv = $('#newOptionsDiv_{!! $language->code !!}');
            var html = '';
            html +='<div class="col-md-3" id="opt_'+i+'_{!! $language->code !!}"><div class="btn-group"><input class="form-control" id="optionsNew" {!! ($language->default == true ? 'required' : null) !!} placeholder="Option Value" value="'+val+'" name="optionsNew['+i+'][{!! $language->code !!}]" type="text"></div>';
            html +='<div class="btn-group"><a class="btn btn-flat btn-danger btn-sm" onclick="removeOption('+i+')" data-original-title="Delete"><i class="fa fa-remove"></i></a></div>';
            $(html).appendTo(newOptionsDiv_{!! $language->code !!});
            @endforeach

        }

        function removeOption(id){
                    @foreach($languages as $language)
            var numInputs = $('#newOptionsDiv_{{$language->code}} :input').size();
            if(numInputs <2){
                toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                return false;
            }
            $('#opt_'+id+'_{!! $language->code !!}').remove();
            @endforeach

                return true;
        }


    </script>
@endsection