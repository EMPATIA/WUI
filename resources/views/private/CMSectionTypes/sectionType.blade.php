@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('CMSectionTypes', trans('privateCMSectionTypes.details'))
                    ->settings(["model" => isset($sectionType) ? $sectionType : null, 'id' => isset($sectionType) ? $sectionType->section_type_key : null])
                    ->show('CMSectionTypesController@edit','CMSectionTypesController@delete', ['key' => isset($sectionType) ? $sectionType->section_type_key : null],'CMSectionTypesController@index')
                    ->create('CMSectionTypesController@store', 'CMSectionTypesController@show', ['key' => isset($sectionType) ? $sectionType->section_type_key : null])
                    ->edit('CMSectionTypesController@update', 'CMSectionTypesController@show', ['key' => isset($sectionType) ? $sectionType->section_type_key : null])
                    ->open();
            @endphp
            {!! Form::oneText('code', trans('privateCMSectionTypes.code'), isset($sectionTypeTypes) ? $sectionTypeTypes->code : null, ['class' => 'form-control', 'id' => 'code','required' => 'required'] ) !!}
            {!! Form::oneSwitch("translatable",trans("privateCMSectionTypes.is_translatable"),isset($sectionType) ? $sectionType->translatable : null,array("name" => "translatable")) !!}
            <div class="row">
                <div class="col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                            <div style="">
                                {!! Form::oneText('name_'.$language->code, ['name' => trans('privateCMSectionTypes.name'),'description' => trans('privateCMSectionTypes.help_name')],($sectionType->translations->{$language->code}->value ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
            </div>

            @if(ONE::actionType('CMSectionTypes') == "create")
                <div class="row">
                    <div class="col-12" style="padding-bottom: 20px">
                        <select id="sectionTypeParameters" name="sectionTypeParameters[]" class="form-control" multiple>
                            @forelse($sectionTypeParameters as $key => $parameter)
                                <option value="{!! $parameter->section_type_parameter_key !!}">{!! $parameter->name !!}</option>
                            @empty
                                {{ trans('privateCMSectionType.no_parameters_available') }}
                            @endforelse
                        </select>
                    </div>
                </div>
            @elseif(ONE::actionType('CMSectionTypes') == "edit")
                    <div class="row">
                    <div class="col-12" style="padding-bottom: 20px">
                        <select id="sectionTypeParameters" name="sectionTypeParameters[]" class="form-control" multiple>
                            @forelse($sectionTypeParameters as $key => $parameter)
                                <option value="{!! $parameter->section_type_parameter_key !!}" @if (array_has($sectionTypeParametersSelected,$parameter->section_type_parameter_key)) selected @endif>{!! $parameter->name !!}</option>
                            @empty
                                {{ trans('privateCMSectionType.no_parameters_available') }}
                            @endforelse
                        </select>
                    </div>
                </div>
            @else
                @forelse ($sectionType->section_type_parameters as $parameter)
                    @if ($loop->first)
                        <hr>
                        <h4 class="box-title">{{ trans('privateCMSectionType.parameters') }}</h4>
                        <table id="parameters" class="table table-hover table-striped dataTable no-footer table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ trans('privateCMSectionType.parameter_key') }}</th>
                                    <th>{{ trans('privateCMSectionType.parameter_code') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                                <tr>
                                    <td>
                                        <a href="{{ action("CMSectionTypeParametersController@show",["key"=>$parameter->section_type_parameter_key]) }}">
                                            {{ $parameter->section_type_parameter_key }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ action("CMSectionTypeParametersController@show",["key"=>$parameter->section_type_parameter_key]) }}">
                                            {{ $parameter->code }}
                                        </a>
                                    </td>
                                </tr>
                    @if ($loop->last)
                            </tbody>
                        </table>
                    @endif
                @empty
                    {{ trans('privateCMSectionType.no_parameters_available') }}
                @endforelse
            @endif
            {!! $form->make() !!}
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $(document).ready(function() {
            @if(ONE::actionType('CMSectionTypes') != "show")
                $("#sectionTypeParameters").select2({
                    'placeholder': "{{ trans('privateCMSectionTypeParameters.select_parameters') }}"
                });
            @else
                $('#parameters').DataTable();
            @endif
        });
    </script>
@endsection